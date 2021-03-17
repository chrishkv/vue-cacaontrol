<template>
  <div>
  <div class="text-center">
    <v-dialog v-model="dialog">
      <v-card>
        <v-card-title class="headline grey lighten-2">
          Total General: {{ getTotalOrdenes }}&nbsp;$
          <v-divider vertical class="mx-4"></v-divider>
          Total Latas: {{ getTotalLatas }}&nbsp;lt
          <v-divider vertical class="mx-4"></v-divider>
          Total Quintales: {{ getTotalQuintales }}&nbsp;qt
        </v-card-title>

        <v-card v-for="(agregar_orden, i) in agregar_ordenes" :key="i">
          <v-row>
            <v-col cols="12" sm="3">
              <v-select :items="tipo_select" filled label="Tipo" v-model="agregar_orden.tipo" item-value="value" background-color="#AFEEEE"></v-select>
            </v-col>
            <v-col cols="12" sm="3">
              <v-text-field v-model="agregar_orden.cantidad" filled type="number" label="Cantidad" :rules="numberRules" background-color="#AFEEEE" required></v-text-field>
            </v-col>
            <v-col cols="12" sm="3">
              <v-text-field v-model="agregar_orden.precio" filled type="number" label="Precio" :rules="numberRules" background-color="#AFEEEE" required></v-text-field>
            </v-col>
            <v-col cols="12" sm="2">
              <v-text-field v-show="agregar_orden.tipo == 1 || agregar_orden.tipo == 3" v-model="agregar_orden.humedad" filled type="number" label="% Humedad" :rules="humedadRules" background-color="#AFEEEE"></v-text-field>
            </v-col>
          </v-row>
          <v-row>
            <v-col cols="12" sm="6">
              <v-text-field v-model="agregar_orden.observacion" filled label="Observacion" background-color="#AFEEEE"></v-text-field>
            </v-col>
            <v-col cols="12" sm="2">
              <v-text-field v-model="agregar_orden.total" filled label="$ Total" :disabled=true background-color="#AFEEEE"></v-text-field>
            </v-col>
          </v-row>
          <v-divider></v-divider>
        </v-card>

        <v-card-actions class="justify-center">
          <v-icon color="green darken-2" @click="agregarOtraOrden">mdi-plus-circle</v-icon>
        </v-card-actions>

        <v-card-actions>
          <v-btn color="primary" text @click="limpiarTodo">Cerrar</v-btn>
          <v-btn v-on:click="addOrden" color="primary" large>Guardar</v-btn>
        </v-card-actions>
      </v-card>
    </v-dialog>
  </div>
  <br>
  <v-snackbar
    v-model="snackbar.visible"
    :color="snackbar.color"
    :timeout="snackbar.timeout"
    :top="true">
      <v-layout align-center pr-4>
        <v-layout column>
          <div>
            <strong>{{ snackbar.title }}</strong>
          </div>
          <div>{{ snackbar.text }}</div>
        </v-layout>
      </v-layout>
  </v-snackbar>
  <v-form>
    <v-container fluid>
      <v-row align="center" justify="center">
        <v-col cols="12" sm="2">
          <v-select :items="tipo_orden_select" filled label="Tipo de Orden" v-model="tipo_orden_id" item-value="value" background-color="#AFEEEE"></v-select>
        </v-col>

        <v-col cols="12" sm="2">
          <v-autocomplete
            label="Nombre y Apellido"
            clearable
            dense
            filled
            v-model="persona_id"
            item-value="id"
            item-text="nombre"
            :items="personas"
            background-color="#AFEEEE"
          ></v-autocomplete>
        </v-col>

        <v-col cols="12" sm="2">
          <v-select :items="sede_select" filled label="Sede" v-model="sede_id" item-value="value" background-color="#AFEEEE"></v-select>
        </v-col>
        <v-btn id="boton_agregar" color="primary" large @click="dialog = true">Agregar</v-btn>
      </v-row>
    </v-container>
  </v-form>
    <v-card>
      <v-card-title>
        <v-text-field
          v-model="search"
          append-icon="mdi-magnify"
          label="Buscar"
          single-line
          hide-details
        ></v-text-field>
      </v-card-title>
      <v-data-table
        :headers="headers"
        :items="ordenes"
        :search="search"
        :footer-props="{
          itemsPerPageText: 'Filas por página:',
          itemsPerPageAllText: 'Todos',
          itemsPerPageOptions: [20,50,100,-1],
        }"
      >
      <template v-slot:item.actions="{ item }">
        <v-icon
          small
          class="mr-2"
          @click="editOrden(item)"
        >
          mdi-pencil
        </v-icon>
        <v-icon
          small
          @click="deleteOrden(item)"
        >
          mdi-delete
        </v-icon>
      </template>
      <template #footer.page-text="props">
        {{props.pageStart}}-{{props.pageStop}} de ~{{props.itemsLength}}
      </template>
      </v-data-table>
    </v-card>
  </div>
</template>
<style scoped>
  #boton_agregar {
    margin-bottom: 30px;
  }

  .row {
    padding-left: 10px;
  }
</style>
<script>
import moment from 'moment'
import axios from 'axios'
import EventBus from '../bus'

  export default {
    created: function() {
        EventBus.$on('add-persona', (item) => {
            this.personas.unshift({id:item[1], nombre:item[0]})
        })
    },

    mounted() {
      this.getOrdenes();
      this.getPersonas();
    },

    computed : {
      getTotalOrdenes: function () {
        let total = parseFloat(this.agregar_ordenes.reduce((acumulador, actual) => acumulador + parseFloat(actual.total), 0)).toFixed(2)
        return isNaN(total) ? '0.00' : total
      },

      getTotalLatas: function () {
        let registro_latas_temporal = this.agregar_ordenes.filter(registro => registro.tipo % 2 == 0)
        let cantidad = parseFloat(registro_latas_temporal.reduce((acumulador, actual) => acumulador + parseFloat(actual.cantidad), 0)).toFixed(2)
        return isNaN(cantidad) ? '0.00' : cantidad
      },

      getTotalQuintales: function () {
        let registro_quintales_temporal = this.agregar_ordenes.filter(registro => registro.tipo % 2 != 0)
        let cantidad = parseFloat(registro_quintales_temporal.reduce((acumulador, actual) => acumulador + parseFloat(actual.cantidad), 0)).toFixed(2)
        return isNaN(cantidad) ? '0.00' : cantidad
      }
    },
    watch: {
      agregar_ordenes: {
        handler(val) {
          val.map((orden) => {
            if (orden.tipo == 1 || orden.tipo == 3) {
              orden.total = parseFloat((orden.cantidad - parseFloat(orden.cantidad * (orden.humedad / 100)).toFixed(2)) * parseFloat(orden.precio / 100).toFixed(2)).toFixed(2);
            } else {
              orden.total = parseFloat(orden.cantidad * orden.precio).toFixed(2);
            }
          })
        },
        deep: true
      },
    },

    methods: {
      addOrden: function () {
        if(this.persona_id != '' && this.revisarOrdenes()) {
          //indica que va a editar
          if(this.orden_id) {
            let orden_temporal = this.agregar_ordenes[0]
            axios.post('./ajaxfile.php', {
              request: 'editar_orden',
              orden_id: this.orden_id,
              persona_id: this.persona_id,
              tipo_orden_id: this.tipo_orden_id,
              sede_id: this.sede_id,
              cantidad: orden_temporal.cantidad,
              humedad: orden_temporal.humedad,
              tipo: orden_temporal.tipo,
              precio: orden_temporal.precio,
              total: orden_temporal.total,
              observacion: orden_temporal.observacion

            })
            .then((response) => {
              if (response.data) {
                this.SnackbarShow("success", "Editado Correctamente."),
                this.getOrdenes(),
                this.limpiarTodo()
              } else {
                this.SnackbarShow("error", "Hubo un Error al guardar, disculpe.")
              }
            })
            .catch((error) => (console.log(error)))
          } else {
            //indica que va a ingresar una orden nueva
            var guardarTodoPromesa = new Promise((resolve, reject) => {
              let persona_id_temporal = this.persona_id
              let tipo_orden_id_temporal = this.tipo_orden_id
              let sede_id_temporal = this.sede_id
              //se recorre cada elemento
              this.agregar_ordenes.forEach((orden) => {
                axios.post('./ajaxfile.php', {
                  request: 'insertar_orden',
                  persona_id: this.persona_id,
                  tipo_orden_id: this.tipo_orden_id,
                  sede_id: this.sede_id,
                  cantidad: orden.cantidad,
                  humedad: orden.humedad,
                  tipo: orden.tipo,
                  precio: orden.precio,
                  total: orden.total,
                  observacion: orden.observacion,
                  fecha: moment(String(new Date())).format('YYYY/MM/DD')
                })
                .then((response) => {
                  if (response.data) {
                    this.SnackbarShow("success", "Guardado Correctamente.")
                    let diminutivo = (orden.tipo % 2 == 0) ? ' lt' : ' qt'
                    this.ordenes.unshift({
                      id: response.data,
                      nombre: this.personas.find((item)=>{return item.id == persona_id_temporal}).nombre,
                      persona_id: persona_id_temporal,
                      tipo_orden_id: tipo_orden_id_temporal ? 'Venta' : 'Compra',
                      sede_nombre: this.sede_select[sede_id_temporal].text,
                      cantidad: orden.cantidad + diminutivo,
                      humedad: orden.humedad + ' %',
                      tipo: this.tipo_select[orden.tipo].text,
                      precio: orden.precio,
                      total: orden.total,
                      observacion: orden.observacion,
                      fecha: moment(String(new Date())).format('YYYY/MM/DD')
                    })
                  } else {
                    this.SnackbarShow("error", "Hubo un Error al guardar, disculpe.")
                  }
                })
                .catch((error) => (console.log(error), reject()))
              })
              resolve(true)
            }).then(() => {
              this.limpiarTodo()
            })
            Promise.all([guardarTodoPromesa])
          }
        } else {
          this.SnackbarShow("warning", "Faltan algunos datos por favor revise.")
        }
      },

      getOrdenes: function() {
        axios.post('ajaxfile.php', {
          request: 'consulta_orden',
         })
         .then(response => (this.ordenes = response.data))
         .catch( (error) => (console.log(error)));
     },

     getPersonas: function() {
       axios.post('./ajaxfile.php', {
         request: 'consulta_personas_select',
        })
        .then(response => (this.personas = response.data))
        .catch((error) => (console.log(error)));
    },

    deleteOrden: function (orden) {
      axios.post('./ajaxfile.php', {
        request: 'eliminar_orden',
        orden_id: orden.id
       })
       .then(response => {
         if (response.data) {
           this.SnackbarShow("success", "Borrado Correctamente"),
           this.getOrdenes()
         } else {
           this.SnackbarShow("error", "Hubo un Error al guardar, disculpe.")
         }
       })
       .catch( (error) => (console.log(error)));
    },

    editOrden: function (orden) {
      this.persona_id = orden.persona_id,
      this.orden_id = orden.id,
      this.tipo_orden_id = (orden.tipo_orden_id == 'Compra') ? 0 : 1,
      this.sede_id = parseInt(orden.sede_id),
      this.agregar_ordenes = [{
        tipo: this.tipo_array_busqueda.indexOf(orden.tipo),
        cantidad: orden.cantidad.match(/^(\.\d+)|(\d+(\.\d+)?)/)[0],
        precio: orden.precio,
        humedad: orden.humedad,
        total: orden.total,
        observacion: orden.observacion,
      }],
      this.dialog = true
    },

    SnackbarShow(tipo, mensaje) {
      if (!tipo) return;
      switch (tipo) {
        case "error":
          this.snackbar = {
            color: "error",
            timeout: 2500,
            title: "Error",
            text: mensaje,
            visible: true
          };
          break;
        case "success":
          this.snackbar = {
            color: "success",
            timeout: 2500,
            title: "Listo",
            text: mensaje,
            visible: true
          };
          break;
        case "warning":
          this.snackbar = {
            color: "warning",
            timeout: 2500,
            title: "¡¡¡Advertencia!!!",
            text: mensaje,
            visible: true
          };
          break;
      }
    },

    agregarOtraOrden () {
      this.agregar_ordenes.push({
        tipo:0,
        cantidad:'',
        precio:'',
        humedad:'',
        total:'',
        observacion:''
      })
    },

    revisarOrdenes () {
      return true
    },

    limpiarTodo () {
      //se cierra el modal
      this.dialog = false,
      //Se borra la informacion de las variables
      this.orden_id = '',
      this.persona_id = '',
      this.tipo_orden_id = 0,
      this.sede_id = 0,
      this.agregar_ordenes = [{
        tipo:0,
        cantidad:'',
        precio:'',
        humedad:'',
        total:'',
        observacion:''
      }]
    },
    },

    data () {
      return {
          persona_id:'',
          orden_id:'',
          tipo_orden_id:0,
          sede_id:0,
          search: '',
          total_total_modal: 0,
          total_cantidad_modal: 0,
          ordenes: [],
          personas: [],
          dialog: false,
          agregar_ordenes: [{
            tipo:0,
            cantidad:'',
            precio:'',
            humedad:'',
            total:'',
            observacion:'',
          }],
          snackbar: {
            color: null,
            text: null,
            timeout: 2500,
            title: null,
            visible: false
          },
          sede_select: [{text:'Quera', value:0},{text:'Rio Negro', value:1},{text:'Machala', value:2},{text:'Guayaquil', value:3}],
          tipo_orden_select: [{text:'Compra', value:0},{text:'Venta', value:1}],
          tipo_select: [
            {text:'Latas CCN51', value:0},
            {text:'Seco CCN51', value:1},
            {text:'Latas Nacional', value:2},
            {text:'Seco Nacional', value:3},
            {text:'Latas Monilla', value:4},
          ],
          tipo_array_busqueda:[
            'Latas CCN51',
            'Seco CCN51',
            'Latas Nacional',
            'Seco Nacional',
            'Latas Monilla'
          ],
          numberRules: [
            v => !!v || 'Valor es Obligatorio',
            v => v > 0 || 'El valor debe ser mayor a 0',
          ],
          humedadRules: [
            v => v > 0 || 'El valor debe ser mayor a 0',
          ],
          headers: [
            {
              text: 'Secuencial',
              align: 'center',
              value: 'id',
            },
            {text: 'Persona Nombre',value: 'nombre' },
            { text: 'Tipo Orden', value: 'tipo_orden_id' },
            { text: 'Sede', value: 'sede_nombre' },
            { text: 'Cantidad', value: 'cantidad' },
            { text: 'Humedad', value: 'humedad' },
            { text: 'Tipo', value: 'tipo' },
            { text: 'Precio', value: 'precio' },
            { text: 'Total', value: 'total' },
            { text: 'Fecha', value: 'fecha' },
            { text: 'Observacion', value: 'observacion' },
            { text: 'Acciones', value: 'actions', sortable: false }
          ],
        }
    },
  };
</script>
