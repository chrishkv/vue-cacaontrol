<template>
  <v-container fluid>
    <v-card>
      <div ref="facturaHtml">
      <v-card>
        <v-row>
          <v-col cols="12" sm="4">
            <v-img
              max-height="120"
              max-width="150"
              src="../../img/cacaontrollogo.png"
              id="logoFactura"
            ></v-img>
            <p>
              <span class="font-weight-black">FACTURA</span><br>
              Serie: 001-001 {{ codigo }}<br>
              Fecha Emisión: {{ fecha }}
            </p>
          </v-col>
          <v-col cols="12" sm="8">
            <p id="informacionPropia">
              <span class="font-weight-black">HUGO PORFIRIO VALDIVIESO ROMERO</span><br>
              R.U.C.: 0701211369001<br>
              AGROINDUSTRIAL BLACK RIVER<br>
              Dir.: Cdla. Bellavista Mz. 65 Solar 29<br>
              Telf.: 04 2203895 - 0999567592<br>
              Guayaquil - Ecuador<br>
              AUT. S.R.I.: 1127144895<br>
              CONTRIBUYENTE RÉGIMEN MICROEMPRESA
            </p>
          </v-col>
        </v-row>
        <v-row>
          <v-col cols="12" sm="4">
              <p>
                Cliente: {{ persona.nombre }}<br>
                R.U.C./C.I.: {{ persona.cedula }}
              </p>
          </v-col>
          <v-col cols="12" sm="8">
              <p>
                Direccion: {{ persona.direccion }}<br>
                Telf.: {{ persona.telefono }}
              </p>
          </v-col>
        </v-row>
        <v-row>
          <v-col cols="12" sm="12">
          <v-simple-table height="300px">
            <template v-slot:default>
              <thead>
                <tr>
                  <th class="text-left">&nbsp;Tipo de Cacao&nbsp;</th>
                  <th class="text-left">&nbsp;Humedad&nbsp;</th>
                  <th class="text-left">&nbsp;Precio&nbsp;</th>
                  <th class="text-left">&nbsp;Cantidad&nbsp;</th>
                  <th class="text-left">&nbsp;Total&nbsp;</th>
                </tr>
              </thead>
              <tbody>
                <tr v-for="orden in ordenes" v-bind:key="orden">
                  <td>{{orden.tipo_nombre}}</td>
                  <td>{{orden.humedad}}<template v-if="orden.humedad">&nbsp;%</template></td>
                  <td>{{orden.precio}}&nbsp;$</td>
                  <td>{{orden.cantidad}}&nbsp;{{(orden.tipo%2 == 0) ? 'lt' : 'qt'}}</td>
                  <td>{{orden.total}}&nbsp;$</td>
                </tr>
                <v-divider></v-divider>
                <tr>
                  <td colspan="4"><strong>Total:</strong>&nbsp;</td>
                  <td><strong>{{ getTotalFactura }}</strong>&nbsp;</td>
                </tr>
              </tbody>
            </template>
          </v-simple-table>
          </v-col>
        </v-row>
      </v-card>
      </div>
      <v-divider></v-divider>
      <v-card-actions>
        <v-btn color="primary" text @click="cerrarDialogo">Cerrar</v-btn>
        <v-btn v-on:click="generatePdf" color="primary" medium>Descargar</v-btn>
      </v-card-actions>
    </v-card>
  </v-container>
</template>
<style scoped>
  .v-application p {
    font-size: 12px;
    margin-bottom: 0px;
  }

  #informacionPropia {
    line-height: 21px;
  }

  p, #logoFactura {
    margin-left: 3px;
  }

  .row {
    margin-top: -10px;
  }
</style>
<script>
import moment from 'moment'
import jsPDF from 'jspdf'

  export default {
    props: ['ordenes', 'persona', 'codigo'],

    data () {
      return {
        fecha: moment(String(new Date())).format('YYYY/MM/DD'),
      }
    },

    computed : {
      getTotalFactura: function () {
        let total = parseFloat(this.ordenes.reduce((acumulador, actual) => acumulador + parseFloat(actual.total), 0)).toFixed(2)
        return isNaN(total) ? '0.00' : total
      },
    },

    methods: {
      cerrarDialogo: function () {
        this.$emit('cerrarDialogo')
      },

      generatePdf: function () {
          let nombre_archivo = 'Factura - numero'
          var doc = new jsPDF('p', 'pt', 'letter')
          doc.html(this.$refs.facturaHtml, {
             callback: function (doc) {
               doc.save(nombre_archivo + '.pdf');
             }
          })
      },
    }
  }

</script>
