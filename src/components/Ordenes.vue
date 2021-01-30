<template>
  <div>
  <br>
  <v-form>
    <v-container>
      <v-row>
        <v-col cols="12" sm="2">
          <v-text-field v-model="comentario" filled label="Nombre Cliente" :rules="nameRules" clearable required></v-text-field>
        </v-col>

        <v-col cols="12" sm="2">
          <v-select :items="tipo_orden_select" filled label="Tipo de Orden" v-model="tipo_orden" item-value="value"></v-select>
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
  export default {
    methods: {
      addOrden: function () {
        this.ordenes.unshift({
          persona_id: this.comentario,
          tipo_orden: this.tipo_orden ? 'Venta' : 'Compra',
          cantidad: this.cantidad,
          tipo: this.tipo,
          precio: this.precio,
          total: this.total,
          observacion: 'Algo escrito',
          fecha: moment(String(new Date())).format('YYYY/MM/DD hh:mm:ss')
        })
      }
    },

    watch: {
      cantidad: function (newVar1) {
        this.total = newVar1 * this.precio;
      },
      precio: function (newVar2) {
        this.total = this.cantidad * newVar2;
      }
  },

    data () {
      return {
          persona_id:'',
          tipo_orden:0,
          cantidad:'',
          tipo:'MOJADO',
          precio:'',
          total:'',
          comentario:'',
          search: '',
          ordenes:[],
          tipo_orden_select: [{text:'Compra', value:0},{text:'Venta', value:1}],
          tipo_select: ['MOJADO','SECO'],
          //reglas para los campos obligatorios
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

          //cabecera para la tabla
          headers: [
            {
              text: 'Persona Nombre',
              align: 'start',
              value: 'persona_id',
            },
            { text: 'Tipo Orden', value: 'tipo_orden' },
            { text: 'Cantidad', value: 'cantidad' },
            { text: 'Tipo', value: 'tipo' },
            { text: 'Precio', value: 'precio' },
            { text: 'Total', value: 'total' },
            { text: 'Fecha', value: 'fecha' },
            { text: 'Observacion', value: 'observacion' },
            { text: 'Acciones', value: 'actions', sortable: false }
          ],
          //valores de prueba para local
          /*ordenes: [
            {
              persona_id: 'Frozen Yogurt',
              tipo_orden: 159,
              cantidad: 6.0,
              tipo: 24,
              precio: 4.0,
              total: '1',
              observacion: 'Algo escrito',
              fecha: '12/28/2021'
            },
            {
              persona_id: 'Ice cream sandwich',
              tipo_orden: 237,
              cantidad: 9.0,
              tipo: 37,
              precio: 4.3,
              total: '1',
              observacion: 'Algo escrito',
              fecha: '12/28/2021'
            },
            {
              persona_id: 'Eclair',
              tipo_orden: 262,
              cantidad: 16.0,
              tipo: 23,
              precio: 6.0,
              total: '7',
              observacion: 'Algo escrito',
              fecha: '12/28/2021'
            },
            {
              persona_id: 'Cupcake',
              tipo_orden: 305,
              cantidad: 3.7,
              tipo: 67,
              precio: 4.3,
              total: '8',
              observacion: 'Algo escrito',
              fecha: '12/28/2021'
            },
            {
              persona_id: 'Gingerbread',
              tipo_orden: 356,
              cantidad: 16.0,
              tipo: 49,
              precio: 3.9,
              total: '16',
              observacion: 'Algo escrito',
              fecha: '12/28/2021'
            },
            {
              persona_id: 'Jelly bean',
              tipo_orden: 375,
              cantidad: 0.0,
              tipo: 94,
              precio: 0.0,
              total: '0',
              observacion: 'Algo escrito',
              fecha: '12/28/2021'
            },
            {
              persona_id: 'Lollipop',
              tipo_orden: 392,
              cantidad: 0.2,
              tipo: 98,
              precio: 0,
              total: '2',
              observacion: 'Algo escrito',
              fecha: '12/28/2021'
            },
            {
              persona_id: 'Honeycomb',
              tipo_orden: 408,
              cantidad: 3.2,
              tipo: 87,
              precio: 6.5,
              total: '45',
              observacion: 'Algo escrito',
              fecha: '12/28/2021'
            },
            {
              persona_id: 'Donut',
              tipo_orden: 452,
              cantidad: 25.0,
              tipo: 51,
              precio: 4.9,
              total: '22',
              observacion: 'Algo escrito',
              fecha: '12/28/2021'
            },
            {
              persona_id: 'KitKat',
              tipo_orden: 518,
              cantidad: 26.0,
              tipo: 65,
              precio: 7,
              total: '6',
              observacion: 'Algo escrito',
              fecha: '12/28/2021'
            },
          ],*/
        }
    },

  };
</script>
