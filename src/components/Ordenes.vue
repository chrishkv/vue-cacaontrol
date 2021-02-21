<template>
  <div>
  <br>
  <v-form>
    <v-container>
      <v-row>
        <v-col cols="12" sm="2">
          <v-select :items="tipo_select" filled label="Tipo" v-model="tipo" item-value="value" background-color="#AFEEEE"></v-select>
        </v-col>

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
          <v-text-field v-model="cantidad" filled type="number" label="Cantidad" :rules="numberRules" background-color="#AFEEEE" required></v-text-field>
        </v-col>

        <v-col cols="12" sm="2">
          <v-text-field v-show="this.tipo == 1 || this.tipo == 3" v-model="humedad" filled type="number" label="% Humedad" :rules="humedadRules" background-color="#AFEEEE"></v-text-field>
        </v-col>
      </v-row>
      <v-row>
        <v-col cols="12" sm="2">
          <v-text-field v-model="precio" filled type="number" label="Precio" :rules="numberRules" background-color="#AFEEEE" required></v-text-field>
        </v-col>

        <v-col cols="12" sm="1">
          <v-text-field v-model="total" filled label="$ Total" :disabled=true background-color="#AFEEEE"></v-text-field>
        </v-col>

        <v-col cols="12" sm="4">
          <v-text-field v-model="observacion" filled label="Observacion" background-color="#AFEEEE"></v-text-field>
        </v-col>

        <v-col cols="12" sm="1">
          <v-btn v-on:click="addOrden" color="primary" large>Guardar</v-btn>
        </v-col>
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
      </v-data-table>
    </v-card>
  </div>
</template>
<style scoped>
  /*.v-text-field__slot {
    background-color:"#AFEEEE" !important;
  }*/
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

    watch: {
      cantidad: function (cantidad) {
        if (this.tipo == 1 || this.tipo == 3) {
          this.total = (cantidad - (parseFloat((cantidad) * (this.humedad / 100))).toFixed(2))  * (parseFloat(this.precio / 100).toFixed(2));
        } else {
          this.total = cantidad * this.precio;
        }
        this.total = parseFloat(this.total).toFixed(2);
      },
      precio: function (precio) {
        if (this.tipo == 1 || this.tipo == 3) {
          this.total = (this.cantidad - (parseFloat((this.cantidad) * (this.humedad / 100))).toFixed(2))  * (parseFloat(precio / 100).toFixed(2));
        } else {
          this.total = precio * this.cantidad;
        }
        this.total = parseFloat(this.total).toFixed(2);
      },
      humedad: function (humedad) {
        if (this.tipo == 1 || this.tipo == 3) {
          this.total = (this.cantidad - (parseFloat((this.cantidad) * (humedad / 100))).toFixed(2))  * (parseFloat(this.precio / 100).toFixed(2));
        } else {
          this.total = this.precio * this.cantidad;
        }
        this.total = parseFloat(this.total).toFixed(2);
      }
    },

    methods: {
      addOrden: function () {
        if(this.persona_id != '' && this.cantidad != '' && this.precio != '' &&
          this.cantidad > 0 && this.precio > 0) {
            //indica que va a editar
          if(this.orden_id) {
            axios.post('./ajaxfile.php', {
              request: 'editar_orden',
              orden_id: this.orden_id,
              persona_id: this.persona_id,
              tipo_orden_id: this.tipo_orden_id,
              cantidad: this.cantidad,
              humedad: this.humedad,
              tipo: this.tipo,
              precio: this.precio,
              total: this.total,
              observacion: this.observacion
            })
            .then((/*response*/) => (
              //console.log(response)
              this.getOrdenes(),
              //Se borra la informacion de las variables
              this.persona_id = '',
              this.tipo_orden_id = 0,
              this.cantidad = '',
              this.humedad = '',
              this.tipo = 0,
              this.precio = '',
              this.total = '',
              this.observacion = ''
            ))
            .catch((error) => (console.log(error)))
          } else {
            //indica que va a ingresar una orden nueva
            axios.post('./ajaxfile.php', {
              request: 'insertar_orden',
              persona_id: this.persona_id,
              tipo_orden_id: this.tipo_orden_id,
              cantidad: this.cantidad,
              humedad: this.humedad,
              tipo: this.tipo,
              precio: this.precio,
              total: this.total,
              observacion: this.observacion,
              fecha: moment(String(new Date())).format('YYYY/MM/DD hh:mm:ss')
            })
            .then((/*response*/) => (
              //console.log(response)
              this.ordenes.unshift({
                nombre: this.personas.find((item)=>{return item.id == this.persona_id}).nombre,
                persona_id:this.persona_id,
                tipo_orden_id: this.tipo_orden_id ? 'Venta' : 'Compra',
                cantidad: this.cantidad + ' lb',
                humedad: this.humedad,
                tipo: this.tipo_select[this.tipo].text,
                precio: this.precio,
                total: this.total,
                observacion: this.observacion,
                fecha: moment(String(new Date())).format('YYYY/MM/DD hh:mm:ss')
              }),
              //Se borra la informacion de las variables
              this.persona_id = '',
              this.tipo_orden_id = 0,
              this.cantidad = '',
              this.humedad = '',
              this.tipo = 0,
              this.precio = '',
              this.total = '',
              this.observacion = ''
            ))
            .catch((error) => (console.log(error)))
          }
        } else {
          alert('Faltan datos.');
        }
      },

      getOrdenes: function() {
        axios.post('ajaxfile.php', {
          request: 'consulta_orden',
         })
         .then(response => (this.ordenes = response.data))
         .catch( (error) => (
           console.log(error)
       ));
     },

     getPersonas: function() {
       axios.post('./ajaxfile.php', {
         request: 'consulta_personas_select',
        })
        .then(response => (this.personas = response.data))
        .catch((error) => (
          console.log(error)
      ));
    },

    deleteOrden: function (orden) {
      axios.post('./ajaxfile.php', {
        request: 'eliminar_orden',
        orden_id: orden.id
       })
       .then(response => (
         console.log(response),
         this.getOrdenes()
       ))
       .catch( (error) => (
         console.log(error)
     ));
    },

    editOrden: function (orden) {
      this.persona_id = orden.persona_id,
      this.orden_id = orden.id,
      this.tipo_orden_id = (orden.tipo_orden_id == 'Compra') ? 0 : 1,
      this.cantidad = orden.cantidad.match(/\d+/)[0],
      this.humedad = orden.humedad,
      this.tipo = this.tipo_array_busqueda.indexOf(orden.tipo),
      this.precio = orden.precio,
      this.observacion = orden.observacion
    },
    },

    data () {
      return {
          persona_id:'',
          orden_id:'',
          tipo_orden_id:0,
          cantidad:'',
          humedad:'',
          tipo:0,
          precio:'',
          total:'',
          observacion:'',
          search: '',
          ordenes: [],
          personas: [],
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
              text: 'Persona Nombre',
              align: 'start',
              value: 'nombre',
            },
            { text: 'Tipo Orden', value: 'tipo_orden_id' },
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
