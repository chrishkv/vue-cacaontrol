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
    <v-container>
      <v-row>
        <v-col cols="12" sm="3">
            <v-date-picker v-model="dates" range></v-date-picker>
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
          <v-select :items="tabla_select" filled label="Selección Orden" v-model="tabla_nombre" item-value="value" v-on:change="buscarRegistros" background-color="#AFEEEE"></v-select>
        </v-col>

        <v-col cols="12" sm="2">
          <v-btn v-on:click="buscarRegistros" color="primary" dark large>Consultar</v-btn>
        </v-col>

        <v-col cols="12" sm="1">
          <img class="clickeable_icon" name="Generar PDF" src="adobe_pdf_document.ico" :height="45" :width="45" cursor="pointer" v-on:click="generatePdf"/>
        </v-col>
      </v-row>
    </v-container>
  </v-form>
  <div ref="tablaHtml">
    <table>
      <thead v-if="persona_id!=''">
        <tr>
          <th>&nbsp;Nombre y Apellido&nbsp;</th>
          <th>&nbsp;Cédula&nbsp;</th>
          </tr>
        </thead>
      <thead v-else-if="tabla_nombre=='orden'">
        <tr>
          <th>&nbsp;Nombre y Apellido&nbsp;</th>
          <th>&nbsp;Cédula&nbsp;</th>
          <th>&nbsp;Tipo de Orden&nbsp;</th>
          <th>&nbsp;Tipo&nbsp;</th>
          <th>&nbsp;Cantidad&nbsp;</th>
          <th>&nbsp;Humedad&nbsp;</th>
          <th>&nbsp;Precio&nbsp;</th>
          <th>&nbsp;Total&nbsp;</th>
          <th>Fecha</th>
        </tr>
      </thead>
      <thead v-else>
        <tr>
          <th>&nbsp;Nombre y Apellido&nbsp;</th>
          <th>&nbsp;Cédula&nbsp;</th>
          <th>&nbsp;Tipo de Orden&nbsp;</th>
          <th>&nbsp;Cantidad&nbsp;</th>
          <th>&nbsp;Observacion&nbsp;</th>
          <th>Fecha</th>
        </tr>
      </thead>
      <tbody v-if="persona_id!=''">
        <tr v-for="info in persona_info" v-bind:key="info">
          <td>{{ info }} </td>
        </tr>
      </tbody>
      <tbody v-else-if="tabla_nombre=='orden'">
        <tr v-for="registro in registros" v-bind:key="registro">
          <td>{{registro.nombre}}</td>
          <td>{{registro.cedula}}</td>
          <td>{{registro.tipo}}</td>
          <td>{{registro.cantidad}}</td>
          <td>{{registro.humedad}}&nbsp;%</td>
          <td>{{registro.precio}}</td>
          <td>{{registro.cantidad}}&nbsp;lb</td>
          <td>{{registro.total}}</td>
          <td>{{registro.fecha}}</td>
        </tr>
      </tbody>
      <tbody v-else>
        <tr v-for="registro in registros" v-bind:key="registro">
          <td>{{registro.nombre}}</td>
          <td>{{registro.cedula}}</td>
          <td>{{registro.tipo_orden}}</td>
          <td>{{registro.cantidad}}</td>
          <td>{{registro.observacion}}</td>
          <td>{{registro.fecha}}</td>
        </tr>
      </tbody>
    </table>
  </div>
  </div>
</template>
<style scoped>
  table, th, td {
    border: 1px solid black;
    border-collapse: collapse;
    text-align:center;
    font-size: 15px;
  }
  table {
    margin: 10px;
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
import EventBus from '../bus'
import moment from 'moment'

  export default {
    created: function() {
        EventBus.$on('add-persona', (item) => {
            this.personas.unshift({id:item[1], nombre:item[0]})
        })
    },

    mounted() {
      this.getPersonas();
    },

    methods: {
      buscarRegistros () {
        if (this.dates.length == 2) {
          axios.post('./ajaxfile.php', {
            request: 'consultar_registros',
            persona_id: this.persona_id,
            tabla_nombre: this.tabla_nombre,
            fechas: this.dates
           })
           .then(response => {
             this.registros = response.data
             console.log(this.registros.length)
             if (this.registros.length == 0)
              this.SnackbarShow("warning", "No hay registros que mostrar.")
           })
           .catch((error) => (console.log(error)));
         } else {
           this.SnackbarShow("warning", "Debe seleccionar correctamente las fechas.")
         }
      },
      getPersonas () {
        axios.post('./ajaxfile.php', {
          request: 'consulta_personas_select',
         })
         .then(response => (this.personas = response.data, console.log(this.personas)))
         .catch((error) => (console.log(error)));
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
    },

    data () {
      return {
        dates: [moment(String(new Date())).subtract(30, 'days').format('YYYY-MM-DD'), moment(String(new Date())).format('YYYY-MM-DD')],
        tabla_nombre:'orden',
        persona_id:'',
        personas: [],
        registros:[],
        tabla_select:[{text:'Ordenes', value:'orden'},{text:'Otras Ordenes', value:'otras_cuentas'}],
        snackbar: {
          color: null,
          text: null,
          timeout: 2500,
          title: null,
          visible: false
        },
      }
    },
  };
</script>
