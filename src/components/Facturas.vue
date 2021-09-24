<template>
  <div>
    <div class="text-center">
      <v-dialog no-click-animation persistent v-model="mostrar_factura" :width="600">
        <Factura
          @cerrarDialogo="closeDialog"
          :ordenes="factura_datos"
          :persona="factura_persona"
          :codigo="factura_codigo"
        />
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
          <v-select :items="tipo_orden_select" filled label="Tipo de Orden" v-model="tipo_orden_id" item-value="value" background-color="#AFEEEE"></v-select>
        </v-col>

        <v-col cols="12" sm="2">
          <v-text-field v-model="cantidad" filled label="$ Total" :rules="cantidadRules" background-color="#AFEEEE" required></v-text-field>
        </v-col>

        <v-col cols="12" sm="4">
          <v-text-field v-model="observacion" filled label="Observacion" :rules="observacionRules" background-color="#AFEEEE" required></v-text-field>
        </v-col>

        <v-col cols="12" sm="1" align="center">
          <v-btn v-on:click="addOtrasCuenta" color="primary" dark large>Buscar</v-btn>
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
        :items="facturas"
        :search="search"
        group-by="secuencial"
        :footer-props="{
          itemsPerPageText: 'Filas por página:',
          itemsPerPageAllText: 'Todos',
          itemsPerPageOptions: [20,50,100,-1],
        }"
      >
      <template v-slot:item.tipo="{ item }">
        {{ tipo_select[item.tipo].text }}
      </template>
      <template v-slot:item.actions="{ item }">
        {{ totalCalculado(item) }}
        <v-icon
          small
          class="mr-2"
          @click="cargarFactura(item)"
        >
          mdi-eye
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
  #app {
      background-color: #5cbbf6 !important;
  }
</style>
<script>
import axios from 'axios'
import Factura from './Factura.vue'

  export default {
    props: ['personas'],
    components: {
      Factura
    },
    mounted() {
      this.getFacturas();
    },
    methods: {
      getFacturas: function() {
        axios.post('ajaxfile.php', {
          request: 'consulta_factura',
         })
         .then(response => (this.facturas = response.data))
         .catch( (error) => (
           console.log(error)
       ));
     },

     totalCalculado: function(factura) {
       let facturas = this.facturas.filter(item => item.secuencial == factura.secuencial)
       let total = parseFloat(facturas.reduce((acumulador, actual) => acumulador + parseFloat(actual.total), 0)).toFixed(2)
       return total
     },

    cargarFactura: function (factura) {
        this.factura_persona = this.personas.find((item)=>{return item.id == factura.persona_id})
        let facturas = this.facturas.filter(item => item.secuencial == factura.secuencial)
        this.factura_datos = facturas.map(item => {
          item.tipo_nombre = this.tipo_select[item.tipo].text
          return item
        })
        this.mostrar_factura = true
    },

    closeDialog: function() {
      this.mostrar_factura = false
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
    },

    data () {
      return {
          mostrar_factura: false,
          factura_datos: null,
          factura_persona: null,
          factura_codigo: 0,
          search: '',
          facturas: [],
          tipo_select: [
            {text:'Latas CCN51', value:0},
            {text:'Seco CCN51', value:1},
            {text:'Latas Nacional', value:2},
            {text:'Seco Nacional', value:3},
            {text:'Latas Monilla', value:4},
          ],
          snackbar: {
            color: null,
            text: null,
            timeout: 2500,
            title: null,
            visible: false
          },
          headers: [
            {
              text: 'Secuencial',
              align: 'center',
              value: 'secuencial',
            },
            { text: 'Persona Nombre', value: 'nombre', sortable: false },
            { text: 'Tipos', value: 'tipo' },
            { text: '$ Total', value: 'total' },
            { text: 'Fecha', value: 'fecha', sortable: false },
            { text: 'Acciones', value: 'actions', sortable: false }
          ],
        }
    },
  };
</script>
