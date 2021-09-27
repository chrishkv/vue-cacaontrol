<template>
  <div>
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
      <v-row>
        <v-col cols="12" sm="3">
            <v-date-picker v-model="dates" range></v-date-picker>
        </v-col>

        <v-col cols="12" sm="2">
          <v-row>
            <v-col cols="12" sm="12">
              <v-select
                :items="tabla_select"
                filled
                label="Selección Orden"
                v-model="tabla_nombre"
                item-value="value"
                v-on:change="cambiarTipoOrden();buscarRegistros();"
                background-color="#AFEEEE">
              </v-select>
            </v-col>
          </v-row>
          <v-row>
            <v-col cols="12" sm="12">
              <v-select v-show="tabla_nombre=='orden'" :items="sede_select" filled label="Sede" v-model="sede_id" item-value="value" background-color="#AFEEEE"></v-select>
            </v-col>
          </v-row>
          <v-row>
            <v-col cols="12" sm="12">
              <v-btn v-on:click="buscarRegistros" color="primary" dark large>Consultar</v-btn>
            </v-col>
          </v-row>
        </v-col>

        <v-col cols="12" sm="2">
          <v-row>
            <v-col cols="12" sm="12">
              <v-select :items="tipo_orden_select" filled label="Tipo de Orden" v-model="tipo_orden_id" item-value="value" background-color="#AFEEEE"></v-select>
            </v-col>
          </v-row>
          <v-row>
            <v-col cols="12" sm="12">
              <v-select v-show="tabla_nombre=='orden'" :items="tipo_select" filled label="Tipo" v-model="tipo_id" item-value="value" background-color="#AFEEEE"></v-select>
            </v-col>
          </v-row>
          <v-row>
            <v-col cols="12" sm="2">
              <img class="clickeable_icon" name="Generar PDF" src="../../img/adobe_pdf_document.png" :height="45" :width="45" cursor="pointer" v-on:click="generatePdf"/>
            </v-col>
            <v-col cols="12" sm="2">
                <download-excel
                  :data="registros"
                  :fields="json_fields"
                  name="cacaontrol">
                  <img src="../../img/excel.png" :height="45" :width="45" class="clickeable_icon" cursor="pointer"/>
                </download-excel>
            </v-col>
          </v-row>
        </v-col>

        <v-col cols="12" sm="2">
          <v-row>
            <v-col cols="12" sm="12">
            <v-autocomplete
              label="Nombre y Apellido"
              clearable
              filled
              v-model="persona_id"
              item-value="id"
              item-text="nombre"
              :items="personas"
              background-color="#AFEEEE"
            ></v-autocomplete>
            </v-col>
          </v-row>
          <v-row>
            <v-col cols="12" sm="12">
              <v-checkbox
                v-show="tabla_nombre=='orden'"
                v-model="check_direccion"
                :label="`Mostrar Dirección`"
              ></v-checkbox>
            </v-col>
          </v-row>
        </v-col>
      </v-row>
    </v-container>
  </v-form>
  <v-card max-width="850px">
  <div ref="tablaHtml" id="tabla_imprimir">
    <v-simple-table v-if="tabla_nombre=='orden' && !persona_id">
      <thead>
        <tr>
          <th>Fecha</th>
          <th>&nbsp;Cédula&nbsp;</th>
          <th>&nbsp;Nombre y Apellido&nbsp;</th>
          <th>&nbsp;Sede&nbsp;</th>
          <th>&nbsp;Tipo de Orden&nbsp;</th>
          <th>&nbsp;Tipo de Cacao&nbsp;</th>
          <th>&nbsp;Humedad&nbsp;</th>
          <th>&nbsp;Precio&nbsp;</th>
          <th>&nbsp;Cantidad&nbsp;</th>
          <th>&nbsp;Total&nbsp;</th>
        </tr>
      </thead>
      <tbody>
        <tr v-for="registro in registros" v-bind:key="registro">
          <td>{{registro.fecha}}</td>
          <td>{{registro.cedula}}</td>
          <td>{{registro.nombre}}</td>
          <td>{{registro.sede_nombre}}</td>
          <td>{{registro.tipo_orden}}</td>
          <td>{{registro.tipo_nombre}}</td>
          <td>{{registro.humedad}}<template v-if="registro.humedad">&nbsp;%</template></td>
          <td>{{registro.precio}}&nbsp;$</td>
          <td>{{registro.cantidad}}&nbsp;{{(registro.tipo%2 == 0) ? 'lt' : 'qt'}}</td>
          <td>{{registro.total}}&nbsp;$</td>
        </tr>
        <tr>
          <td colspan="5" style="text-align:right"><strong>Total Quintales:</strong>&nbsp;</td>
          <td>{{total_quintales}}&nbsp;qt</td>
          <td><strong>Total Latas:</strong>&nbsp;</td>
          <td>{{total_latas}}&nbsp;lt</td>
          <td><strong>Total:</strong>&nbsp;</td>
          <td>{{total_total}}&nbsp;$</td>
        </tr>
      </tbody>
    </v-simple-table>
    <template v-else-if="tabla_nombre=='orden' && persona_id">
      <v-simple-table>
        <tr>
          <th>Nombre y Apellido</th>
          <td>{{registros[0].nombre}}</td>
        </tr>
        <tr>
          <th>Cedula</th>
          <td>{{registros[0].cedula}}</td>
        </tr>
        <tr>
          <th>Telefono</th>
          <td>{{registros[0].telefono}}</td>
        </tr>
      </v-simple-table>
      <v-simple-table v-show="check_direccion">
        <tr>
          <th>hectarea</th>
          <td>{{registros[0].hectarea}}</td>
        </tr>
        <tr>
          <th>Direccion</th>
          <td>{{registros[0].direccion}}</td>
        </tr>
        <tr>
          <th>Direccion de parcela</th>
          <td>{{registros[0].parcela}}</td>
        </tr>
      </v-simple-table>
      <v-simple-table>
        <thead>
          <tr>
            <th>Fecha</th>
            <th>&nbsp;Sede&nbsp;</th>
            <th>&nbsp;Tipo de Orden&nbsp;</th>
            <th>&nbsp;Tipo de Cacao&nbsp;</th>
            <th>&nbsp;Humedad&nbsp;</th>
            <th>&nbsp;Precio&nbsp;</th>
            <th>&nbsp;Cantidad&nbsp;</th>
            <th>&nbsp;Total&nbsp;</th>
          </tr>
        </thead>
        <tbody>
          <tr v-for="registro in registros" v-bind:key="registro">
            <td>{{registro.fecha}}</td>
            <td>{{registro.sede_nombre}}</td>
            <td>{{registro.tipo_orden}}</td>
            <td>{{registro.tipo_nombre}}</td>
            <td>{{registro.humedad}}<template v-if="registro.humedad">&nbsp;%</template></td>
            <td>{{registro.precio}}&nbsp;$</td>
            <td>{{registro.cantidad}}{{(registro.tipo % 2 == 0) ? ' lt' : ' qt'}}</td>
            <td>{{registro.total}}&nbsp;$</td>
          </tr>
          <tr>
            <td colspan="3" style="text-align:right"><strong>Total Quintales:</strong>&nbsp;</td>
            <td>{{total_latas}}&nbsp;qt</td>
            <td><strong>Total Latas:</strong>&nbsp;</td>
            <td>{{total_latas}}&nbsp;lt</td>
            <td><strong>Total:</strong>&nbsp;</td>
            <td>{{total_total}}&nbsp;$</td>
          </tr>
        </tbody>
      </v-simple-table>
    </template>
    <v-simple-table v-else>
      <thead >
        <tr>
          <th>&nbsp;Nombre y Apellido&nbsp;</th>
          <th>&nbsp;Cédula&nbsp;</th>
          <th>&nbsp;Tipo de Orden&nbsp;</th>
          <th>&nbsp;Cantidad&nbsp;</th>
          <th>&nbsp;Observacion&nbsp;</th>
          <th>Fecha</th>
        </tr>
      </thead>
      <tbody>
        <tr v-for="registro in registros" v-bind:key="registro">
          <td>{{registro.nombre}}</td>
          <td>{{registro.cedula}}</td>
          <td>{{registro.tipo_orden}}</td>
          <td>{{registro.cantidad}}</td>
          <td>{{registro.observacion}}</td>
          <td>{{registro.fecha}}</td>
        </tr>
        <tr>
          <td colspan="5" style="text-align:right"><strong>Cantidad:</strong>&nbsp;</td>
          <td>{{total_latas}}</td>
        </tr>
      </tbody>
    </v-simple-table>
  </div>
  </v-card>
  </div>
</template>
<style scoped>
  table, th, td {
    border: 1px solid black;
    border-collapse: collapse;
    text-align:center;
    font-size: 15px;
    padding: 2px;
  }

  #tabla_imprimir {
    margin: 5px;
  }

  .clickeable_icon {
    cursor: pointer;
  }

  input[type="checkbox"]
  {
      margin-top: 2%;
      margin-right: 5px;
      vertical-align: middle;
  }
</style>
<script>
import axios from 'axios'
import jsPDF from 'jspdf'
import moment from 'moment'
import Vue from "vue";
import JsonExcel from "vue-json-excel";

Vue.component("downloadExcel", JsonExcel);

  export default {
    props: ['personas'],
    methods: {
      buscarRegistros () {
        if (this.dates.length == 2) {
          axios.post('./ajaxfile.php', {
            request: 'consultar_registros',
            tabla_nombre: this.tabla_nombre,
            tipo_orden_id: this.tipo_orden_id,
            tipo_id: this.tipo_id,
            persona_id: this.persona_id,
            sede_id: this.sede_id,
            fechas: this.dates
           })
           .then(response => {
             this.registros = response.data
             if (this.registros.length == 0)
              this.SnackbarShow("warning", "No hay registros que mostrar.")
             else if (this.tabla_nombre == 'orden') {
               let registro_latas_temporal = this.registros.filter(registro => registro.tipo % 2 == 0)
               this.total_latas = parseFloat(registro_latas_temporal.reduce((acumulador, actual) => acumulador + parseFloat(actual.cantidad), 0)).toFixed(2)
               let registro_quintales_temporal = this.registros.filter(registro => registro.tipo % 2 != 0)
               this.total_quintales = parseFloat(registro_quintales_temporal.reduce((acumulador, actual) => acumulador + parseFloat(actual.cantidad), 0)).toFixed(2)
               if (this.tabla_nombre == "orden")
                  this.total_total = parseFloat(this.registros.reduce((acumulador, actual) => acumulador + parseFloat(actual.total), 0)).toFixed(2)
             } else {
               this.total_latas = parseFloat(this.registros.reduce((acumulador, actual) => acumulador + parseFloat(actual.cantidad), 0)).toFixed(2)
             }
           })
           .catch((error) => (console.log(error)));
         } else {
           this.SnackbarShow("warning", "Debe seleccionar correctamente las fechas.")
         }
      },
      cambiarTipoOrden () {
        if (this.tabla_nombre=="orden") {
          this.tipo_orden_select = [{text:'Todo', value:""},{text:'Compra', value:0},{text:'Venta', value:1}]
          this.json_fields = {
            Fecha: "fecha",
            Cedula: "cedula",
            "Nombre y Apellido": "nombre",
            Sede: "sede_nombre",
            "Tipo de Orden": "tipo_orden",
            "Tipo de Cacao": "tipo_nombre",
            Humedad: "humedad",
            Precio: "precio",
            Cantidad: "cantidad",
            Total: "total",
          }
        } else {
          this.tipo_orden_select = [{text:'Todo', value:""},{text:'Compra', value:0},{text:'Venta', value:1},{text:'Gasto', value:2}]
          this.json_fields = {
            Fecha: "fecha",
            Cedula: "cedula",
            "Nombre y Apellido": "nombre",
            "Tipo de Orden": "tipo_orden",
            Cantidad: "cantidad",
            Observacion: "observacion"
          }
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

      generatePdf: function () {
        if (this.dates.length == 2) {
          var nombre_archivo = this.tabla_nombre + '-' + this.dates[0] + '-' + this.dates[1]
          var doc = new jsPDF('l', 'pt', 'A4')
          doc.html(this.$refs.tablaHtml, {
             callback: function (doc) {
               doc.save(nombre_archivo + '.pdf');
             }
          })
        } else {
          this.SnackbarShow("warning", "Debe seleccionar correctamente las fechas.")
        }
      },

      generateExcel: function () {
        this.SnackbarShow("error", "Funcion aún en construcción")
      },
    },

    data () {
      return {
        check_direccion:true,
        total_latas:0,
        total_quintales:0,
        total_total:0,
        persona_id:null,
        sede_id:null,
        dates: [moment(String(new Date())).subtract(30, 'days').format('YYYY-MM-DD'), moment(String(new Date())).format('YYYY-MM-DD')],
        tipo_orden_id: null,
        tipo_id: null,
        tabla_nombre:'orden',
        registros:[],
        tipo_orden_select: [{text:'Todo', value:""},{text:'Compra', value:0},{text:'Venta', value:1}],
        sede_select: [{text:'Todo', value:""},{text:'Quera', value:0},{text:'Rio Negro', value:1},{text:'Machala', value:2},{text:'Guayaquil', value:3}],
        tabla_select:[{text:'Ordenes', value:'orden'},{text:'Otras Ordenes', value:'otras_cuentas'}],
        tipo_select: [
          {text:'Todo', value:""},
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
        json_fields: {
          Fecha: "fecha",
          Cedula: "cedula",
          "Nombre y Apellido": "nombre",
          Sede: "sede_nombre",
          "Tipo de Orden": "tipo_orden",
          "Tipo de Cacao": "tipo_nombre",
          Humedad: "humedad",
          Precio: "precio",
          Cantidad: "cantidad",
          Total: "total",
        },
      }
    },
  };
</script>
