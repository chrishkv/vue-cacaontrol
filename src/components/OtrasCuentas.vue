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
          <v-select :items="tipo_orden_select" filled label="Tipo de Orden" v-model="tipo_orden_id" item-value="value"></v-select>
        </v-col>

        <v-col cols="12" sm="2">
          <v-text-field v-model="cantidad" filled label="$ Total" :rules="cantidadRules" required></v-text-field>
        </v-col>

        <v-col cols="12" sm="4">
          <v-text-field v-model="observacion" filled label="Observacion" :rules="observacionRules" required></v-text-field>
        </v-col>

        <v-col cols="12" sm="1">
          <v-btn v-on:click="addOtrasCuenta" color="primary" dark large>Guardar</v-btn>
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
        :items="otrasCuentas"
        :search="search"
      >
      <template v-slot:item.actions="{ item }">
        <v-icon
          small
          class="mr-2"
          @click="editOtrasCuenta(item)"
        >
          mdi-pencil
        </v-icon>
        <v-icon
          small
          @click="deleteOtrasCuenta(item)"
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
      this.getOtrasCuentas();
      this.getPersonas();
    },

    methods: {
      addOtrasCuenta: function () {
        if(this.persona_id != '' && this.cantidad != '' && this.observacion != '') {
          //indica que va a editar
          console.log(this.otras_cuenta_id);
          if(this.otras_cuenta_id) {
            axios.post('./ajaxfile.php', {
              request: 'editar_otras_cuenta',
              otras_cuenta_id: this.otras_cuenta_id,
              persona_id: this.persona_id,
              tipo_orden_id: this.tipo_orden_id,
              cantidad: this.cantidad,
              observacion: this.observacion
            })
            .then((/*response*/) => (
              //console.log(response)
              this.getOtrasCuentas(),
              //Se borra la informacion de las variables
              this.otras_cuenta_id = '',
              this.persona_id = '',
              this.tipo_orden_id = 2,
              this.cantidad = '',
              this.observacion = ''
            ))
            .catch((error) => (console.log(error)))
          } else {
            //indica que va a ingresar una cuenta nueva
            axios.post('./ajaxfile.php', {
              request: 'insertar_otras_cuenta',
              persona_id: this.persona_id,
              tipo_orden_id: this.tipo_orden_id,
              cantidad: this.cantidad,
              observacion: this.observacion,
              fecha: moment(String(new Date())).format('YYYY/MM/DD hh:mm:ss')
            })
            .then((response) => (
              this.otrasCuentas.unshift({
                otras_cuenta_id: response.data,
                nombre: this.personas.find((item)=>{return item.id == this.persona_id}).nombre,
                persona_id: this.persona_id,
                tipo_orden_id: this.tipo_orden_id,
                cantidad: this.cantidad,
                observacion: this.observacion,
                fecha: moment(String(new Date())).format('YYYY/MM/DD hh:mm:ss')
              }),
              //Se borra la informacion de las variables
              this.otras_cuenta_id = '',
              this.persona_id = '',
              this.tipo_orden_id = 2,
              this.cantidad = '',
              this.observacion = ''
            ))
            .catch((error) => (console.log(error)))
          }
        } else {
          alert('Faltan datos.');
        }
      },

      getOtrasCuentas: function() {
        axios.post('ajaxfile.php', {
          request: 'consulta_otras_cuenta',
         })
         .then(response => (this.otrasCuentas = response.data))
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

    deleteOtrasCuenta: function (otrasCuentas) {
      axios.post('./ajaxfile.php', {
        request: 'eliminar_otras_cuenta',
        otras_cuenta_id: otrasCuentas.id
       })
       .then(response => (
         console.log(response),
         this.getOtrasCuentas()
       ))
       .catch( (error) => (
         console.log(error)
     ));
    },

    editOtrasCuenta: function (otrasCuentas) {
      console.log(otrasCuentas);
      this.otras_cuenta_id = otrasCuentas.id,
      this.persona_id = otrasCuentas.persona_id,
      this.tipo_orden_id = Number(otrasCuentas.tipo_orden_id),
      this.cantidad = otrasCuentas.cantidad,
      this.observacion = otrasCuentas.observacion
    },
    },

    data () {
      return {
          otras_cuenta_id:'',
          persona_id:'',
          tipo_orden_id:2,
          cantidad:'',
          observacion:'',
          search:'',
          otrasCuentas:[],
          personas:[],
          tipo_orden_select:[{text:'Compra', value:0},{text:'Venta', value:1},{text:'Gasto', value:2}],
          cantidadRules:[
            v => !!v || 'Valor es Obligatorio',
            v => v > 0 || 'El valor debe ser mayor a 0',
          ],
          observacionRules:[
            v => !!v || 'Observación es Obligatorio',
            v => v.length >= 5 || 'Nombre debe ser mayor a 5 Caracteres',
          ],
          headers:[
            {
              text: 'Persona Nombre',
              align: 'start',
              value: 'nombre',
            },
            { text: 'Tipo', value: 'tipo_orden_nombre' },
            { text: '$ Total', value: 'cantidad' },
            { text: 'Observacion', value: 'observacion' },
            { text: 'Fecha', value: 'fecha' },
            { text: 'Acciones', value: 'actions', sortable: false }
          ],
        }
    },
  };
</script>
