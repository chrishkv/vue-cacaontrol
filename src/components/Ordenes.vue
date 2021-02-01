<template>
  <div>
  <br>
  <v-form>
    <v-container>
      <v-row>
        <v-col cols="12" sm="2">
          <v-text-field v-model="observacion" filled label="Nombre Cliente" :rules="nameRules" clearable required></v-text-field>
        </v-col>

        <v-col cols="12" sm="2">
          <v-select :items="tipo_orden_select" filled label="Tipo de Orden" v-model="tipo_orden_id" item-value="value"></v-select>
        </v-col>

        <v-col cols="12" sm="2">
          <v-text-field v-model="cantidad" filled type="number" label="Cantidad" :rules="numberRules" required></v-text-field>
        </v-col>

        <v-col cols="12" sm="2">
          <v-select :items="tipo_select" filled label="Tipo" v-model="tipo"></v-select>
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
    </v-container>
  </v-form>
    <v-card>
      <v-card-title>
        <v-text-field
          v-model="search"
          append-icon="mdi-magnify"
          label="Search"
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
          @click="deleteItem(item)"
        >
          mdi-delete
        </v-icon>
      </template>
      </v-data-table>
    </v-card>
  </div>
</template>
<style scoped>
  /*.ma-2 {
    background-color: #5cbbf6 !important;
  }*/
</style>
<script>
import moment from 'moment'
import axios from 'axios'
  export default {
    mounted() {
      this.allRecords();
    },

    watch: {
      cantidad: function (newVar1) {
        this.total = newVar1 * this.precio;
      },
      precio: function (newVar2) {
        this.total = this.cantidad * newVar2;
      }
    },

    methods: {
      addOrden: function () {
        if(this.observacion != '' && this.cantidad != '' && this.precio != '' &&
          this.cantidad > 0 && this.precio > 0 && this.observacion.length >= 10) {
          axios.post('./ajaxfile.php', {
            request: 2,
            persona_id: 0,
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
            this.ordenes.unshift({
              persona_id: this.observacion,
              tipo_orden_id: this.tipo_orden_id ? 'Venta' : 'Compra',
              cantidad: this.cantidad + ' lb',
              tipo: this.tipo,
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

      allRecords: function() {
        axios.post('./ajaxfile.php', {
         request: 1
         })
         .then(response => (this.ordenes = response.data))
         .catch(error => (//console.log(error)
           this.ordenes = [
             {
               persona_id: error,
               tipo_orden_id: 0,
               cantidad: 6.0,
               tipo: 24,
               precio: 4.0,
               total: '1',
               observacion: 'Algo escrito',
               fecha: '12/28/2021'
             },
             {
               persona_id: 'Ice cream sandwich',
               tipo_orden_id: 1,
               cantidad: 9.0,
               tipo: 37,
               precio: 4.3,
               total: '1',
               observacion: 'Algo escrito',
               fecha: '12/28/2021'
             },
             {
               persona_id: 'Eclair',
               tipo_orden_id: 0,
               cantidad: 16.0,
               tipo: 23,
               precio: 6.0,
               total: '7',
               observacion: 'Algo escrito',
               fecha: '12/28/2021'
             }]
       ));
     },
    },

    data () {
      return {
          persona_id:'',
          tipo_orden_id:0,
          cantidad:'',
          tipo:'MOJADO',
          precio:'',
          total:'',
          observacion:'',
          search: '',
          ordenes: [],
          tipo_orden_select: [{text:'Compra', value:0},{text:'Venta', value:1}],
          tipo_select: ['MOJADO','SECO'],
          nameRules: [
            v => !!v || 'Nombre es Obligatorio',
            v => v.length >= 10 || 'Nombre debe ser mayor a 10 Caracteres',
          ],
          numberRules: [
            v => !!v || 'Valor es Obligatorio',
            v => v > 0 || 'El valor debe ser mayor a 0',
          ],
          /*emailRules: [
            v => /.+@.+/.test(v) || 'E-mail must be valid',
          ],*/
          headers: [
            {
              text: 'Persona Nombre',
              align: 'start',
              value: 'observacion',
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
