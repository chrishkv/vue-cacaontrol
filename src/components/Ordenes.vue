<template>
  <div>
  <br>
  <v-form>
    <v-container>
      <v-row>
        <v-col cols="12" sm="2">
          <v-text-field v-model="comentario" filled label="Persona Nombre" clearable></v-text-field>
        </v-col>

        <v-col cols="12" sm="2">
          <v-select :items="tipo_orden_select" filled label="Tipo de Orden" v-model="tipo_orden" item-value="value"></v-select>
        </v-col>

        <v-col cols="12" sm="2">
          <v-text-field v-model="cantidad" filled type="number" label="Cantidad" clearable></v-text-field>
        </v-col>

        <v-col cols="12" sm="2">
          <v-select :items="tipo_select" filled label="Tipo de Orden" v-model="tipo"></v-select>
        </v-col>

        <v-col cols="12" sm="2">
          <v-text-field v-model="precio" filled type="number" label="Precio" clearable></v-text-field>
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
      ></v-data-table>
    </v-card>
  </div>
</template>
<style scoped>
  /*.ma-2 {
    background-color: #5cbbf6 !important;
  }*/
</style>
<script>
  export default {
    methods: {
      addOrden: function () {
        this.ordenes.push({
          persona_id: this.comentario,
          tipo_orden: this.tipo_orden,
          cantidad: this.cantidad,
          tipo: this.tipo,
          precio: this.precio,
          total: this.total,
          observacion: 'Algo escrito',
          fecha: new Date()
        })
        //alert('Hello ' + this.tipo_orden + '!')
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
          tipo_orden_select: [{text:'Compra', value:0},{text:'Venta', value:1}],
          tipo_select: ['SECO', 'MOJADO'],
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
          ],
          ordenes: [
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
          ],
        }
    },

  };
</script>
