<template>
  <div>
  <br>
  <v-form>
    <v-container>
      <v-row>
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
          ></v-autocomplete>
        </v-col>

        <v-col cols="12" sm="2">
          <v-select :items="tipo_select" filled label="Tipo" v-model="tipo" item-value="value"></v-select>
        </v-col>

        <v-col cols="12" sm="2">
          <v-select :items="tipo_orden_select" filled label="Tipo de Orden" v-model="tipo_orden_id" item-value="value"></v-select>
        </v-col>

        <v-col cols="12" sm="2">
          <v-text-field v-model="cantidad" filled type="number" label="Cantidad" :rules="numberRules" required></v-text-field>
        </v-col>

        <v-col cols="12" sm="2">
          <v-text-field v-model="precio" filled type="number" label="Precio" :rules="numberRules" required></v-text-field>
        </v-col>

        <v-col cols="12" sm="1">
          <v-text-field v-model="total" filled label="Total" :disabled=true></v-text-field>
        </v-col>

        <v-col cols="12" sm="1">
          <v-btn v-on:click="addOrden" color="primary" dark large>Guardar</v-btn>
        </v-col>
      </v-row>
      <v-row>
        <v-col cols="12" sm="11">
          <v-text-field v-model="observacion" filled label="Observacion"></v-text-field>
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
          @click="editItem(item)"
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
  #app {
      background-color: #5cbbf6 !important;
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

    watch: {
      cantidad: function (newVar1) {
        this.total = newVar1 * this.precio;
        this.total = parseFloat(this.total).toFixed(2);
      },
      precio: function (newVar2) {
        this.total = this.cantidad * newVar2;
        this.total = parseFloat(this.total).toFixed(2);
      }
    },

    methods: {
      addOrden: function () {
        if(this.persona_id != '' && this.cantidad != '' && this.precio != '' &&
          this.cantidad > 0 && this.precio > 0) {
          axios.post('./ajaxfile.php', {
            request: 'insertar_orden',
            persona_id: this.persona_id,
            tipo_orden_id: this.tipo_orden_id,
            cantidad: this.cantidad,
            tipo: this.tipo,
            precio: this.precio,
            total: this.total,
            observacion: this.observacion,
            fecha: moment(String(new Date())).format('YYYY/MM/DD hh:mm:ss')
          })
          .then((/*response*/) => (
            //console.log(response)
            console.log(this.personas.find((item)=>{return item.id == this.persona_id}).nombre),
            this.ordenes.unshift({
              persona_id: this.personas.find((item)=>{return item.id == this.persona_id}).nombre,
              tipo_orden_id: this.tipo_orden_id ? 'Venta' : 'Compra',
              cantidad: this.cantidad + ' lb',
              tipo: this.tipo_select[this.tipo].text,
              precio: this.precio,
              total: this.total,
              observacion: this.observacion,
              fecha: moment(String(new Date())).format('YYYY/MM/DD hh:mm:ss')
            })
          ))
          .catch(error => (console.log(error)))
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
         request: 'consulta_personas_orden',
        })
        .then(response => (this.personas = response.data))
        .catch( (error) => (
          console.log(error),
          this.personas =[
            {id:0, nombre:'primero'},
            {id:1, nombre:'segundo'},
            {id:2, nombre:'tercero'}
          ]
      ));
    },

    deleteOrden: function (orden) {
      console.log(orden)
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
    },

    data () {
      return {
          persona_id:'',
          orden_id:'',
          tipo_orden_id:0,
          cantidad:'',
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
            {text:'Seco CCN51 lbs', value:1},
            {text:'Latas Nacional', value:2},
            {text:'Seco Nacional', value:3},
            {text:'Latas Monilla', value:4},
          ],
          numberRules: [
            v => !!v || 'Valor es Obligatorio',
            v => v > 0 || 'El valor debe ser mayor a 0',
          ],
          headers: [
            {
              text: 'Persona Nombre',
              align: 'start',
              value: 'persona_id',
            },
            { text: 'Tipo Orden', value: 'tipo_orden_id' },
            { text: 'Cantidad', value: 'cantidad' },
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
