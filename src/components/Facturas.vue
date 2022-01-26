<template>
  <div>
    <div class="text-center">
      <v-dialog no-click-animation persistent v-model="mostrar_factura" :width="1200">
        <div ref="facturaHtmlImprimir" v-show="mostrar_imprimir_factura">
          <v-card>
            <v-row>
              <v-col cols="12" sm="6">
                <Factura
                  :ordenes="factura.datos"
                  :persona="factura.persona"
                  :codigo="factura.codigo"
                />
              </v-col>
              <v-col cols="12" sm="6">
                <Factura
                  :ordenes="factura.datos"
                  :persona="factura.persona"
                  :codigo="factura.codigo"
                />
              </v-col>
            <v-row>
          </v-card>
        </div>
      </v-dialog>
      <v-dialog no-click-animation persistent v-model="mostrar_factura" :width="600">
        <v-card>
        <Factura
          :ordenes="factura.datos"
          :persona="factura.persona"
          :codigo="factura.codigo"
        />
        <v-divider></v-divider>
        <v-card-actions>
          <v-btn color="primary" text @click="closeDialog">Cerrar</v-btn>
          <v-btn v-on:click="generatePdf" color="primary" medium>Descargar</v-btn>
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
      <v-row align="start" justify="center">
        <v-col cols="12" sm="2">
          <v-autocomplete
            label="Nombre y Apellido"
            clearable
            dense
            filled
            v-model="form.persona_id"
            item-value="id"
            item-text="nombre"
            :items="personas"
            background-color="#AFEEEE"
          ></v-autocomplete>
        </v-col>

        <v-col cols="12" sm="2">
          <v-select :items="tipo_select" filled label="Tipo" v-model="form.tipo_id" item-value="value" background-color="#AFEEEE" clearable></v-select>
        </v-col>

        <v-col cols="12" sm="2">
          <v-select :items="sede_select" filled label="Sede" v-model="form.sede_id" item-value="value" background-color="#AFEEEE" clearable></v-select>
        </v-col>

        <v-col cols="12" sm="4">
          <v-dialog
          ref="dialog_date"
          v-model="modal_date"
          :return-value.sync="form.dates"
          persistent
          width="290px"
        >
          <template v-slot:activator="{ on, attrs }">
            <v-text-field
              v-model="form.dates"
              label="Fecha de la Factura"
              prepend-icon="mdi-calendar"
              readonly
              v-bind="attrs"
              v-on="on"
            ></v-text-field>
          </template>
          <v-date-picker
            v-model="form.dates"
            range
            scrollable
          >
            <v-spacer></v-spacer>
            <v-btn
              text
              color="primary"
              @click="modal_date = false"
            >
              Cerrar
            </v-btn>
            <v-btn
              text
              color="primary"
              @click="$refs.dialog_date.save(form.dates)"
            >
              OK
            </v-btn>
          </v-date-picker>
        </v-dialog>
        </v-col>

        <v-col cols="12" sm="1">
          <v-btn v-on:click="buscarFacturas" color="primary" dark large>Buscar</v-btn>
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
      <template v-slot:item.sede="{ item }">
        {{ sede_select[item.sede].text }}
      </template>
      <template v-slot:item.actions="{ item }">
        <v-icon
          small
          class="mr-2"
          @click="cargarFactura(item)"
        >
          mdi-eye
        </v-icon>
        {{ totalCalculado(item) }}&nbsp;$
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
import moment from 'moment'
import jsPDF from 'jspdf'
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
        this.factura.persona = this.personas.find((item)=>{return item.id == factura.persona_id})
        this.factura.codigo = factura.secuencial
        let facturas = this.facturas.filter(item => item.secuencial == factura.secuencial)
        this.factura.datos = facturas.map(item => {
          item.tipo_nombre = this.tipo_select[item.tipo].text
          return item
        })
        this.mostrar_factura = true
    },

    closeDialog: function() {
      this.mostrar_factura = false
    },

    buscarFacturas () {
      if (this.form.dates.length == 2) {
        axios.post('./ajaxfile.php', {
          request: 'consultar_facturas',
          ...this.form
         })
         .then(response => {
           this.facturas = response.data
           console.log(this.facturas)
           if (this.facturas.length == 0)
            this.SnackbarShow("warning", "No hay registros que mostrar.")
         })
         .catch((error) => (console.log(error)));
       } else {
         this.SnackbarShow("warning", "Debe seleccionar correctamente las fechas.")
       }
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

    generatePdf: async function () {
        this.mostrar_imprimir_factura = true
        const nombre_archivo = 'Factura-' + this.factura.codigo
        var doc = new jsPDF('p', 'pt', [1200, 1200])
        await doc.html(this.$refs.facturaHtmlImprimir, {
           callback: function (doc) {
             doc.save(nombre_archivo + '.pdf');
           }
        })
        this.mostrar_imprimir_factura = false
    },
    },

    data () {
      return {
          form: {
            dates: [moment(String(new Date())).subtract(30, 'days').format('YYYY-MM-DD'), moment(String(new Date())).format('YYYY-MM-DD')],
            persona_id: null,
            tipo_id: null,
            sede_id: null,
          },
          factura: {
            datos: null,
            persona: null,
            codigo: null,
          },
          modal_date: false,
          mostrar_factura: false,
          mostrar_imprimir_factura: false,
          search: '',
          facturas: [],
          tipo_select: [
            {text:'Latas CCN51', value:0},
            {text:'Seco CCN51', value:1},
            {text:'Latas Nacional', value:2},
            {text:'Seco Nacional', value:3},
            {text:'Latas Monilla', value:4},
          ],
          sede_select: [
            {text:'Quera', value:0},
            {text:'Rio Negro', value:1},
            {text:'Machala', value:2},
            {text:'Guayaquil', value:3}
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
            { text: 'Persona Nombre', value: 'nombre' },
            { text: 'Sede', value: 'sede' },
            { text: 'Tipos', value: 'tipo' },
            { text: '$ Total', value: 'total' },
            { text: 'Fecha', value: 'fecha', sortable: false },
            { text: 'Acciones', value: 'actions', sortable: false }
          ],
        }
    },
  };
</script>
