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
            background-color="#AFEEEE"
          ></v-autocomplete>
        </v-col>

        <v-col cols="12" sm="2">
          <v-text-field v-model="numero" filled type="number" label="Número de Cuenta" :rules="cuentaRules" background-color="#AFEEEE" required></v-text-field>
        </v-col>

        <v-col cols="12" sm="4">
          <v-autocomplete
            label="Nombre de Banco"
            clearable
            dense
            filled
            v-model="nombre_banco"
            item-value="nombre"
            item-text="nombre"
            :items="bancos"
            background-color="#AFEEEE"
          ></v-autocomplete>
        </v-col>

        <v-col cols="12" sm="1">
          <v-btn v-on:click="addCuenta" color="primary" dark large>Guardar</v-btn>
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
        :items="cuentas"
        :search="search"
      >
      <template v-slot:item.actions="{ item }">
        <v-icon
          small
          class="mr-2"
          @click="editCuenta(item)"
        >
          mdi-pencil
        </v-icon>
        <v-icon
          small
          @click="deleteCuenta(item)"
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
import axios from 'axios'
import EventBus from '../bus'

  export default {
    created: function() {
        EventBus.$on('add-persona', (item) => {
            this.personas.unshift({id:item[1], nombre:item[0]})
        })
    },

    mounted() {
      this.getCuentas();
      this.getPersonas();
    },

    methods: {
      addCuenta: function () {
        if(this.persona_id != '' && this.numero != '') {
          //indica que va a editar
          console.log(this.cuenta_id);
          if(this.cuenta_id) {
            axios.post('./ajaxfile.php', {
              request: 'editar_cuenta',
              cuenta_id: this.cuenta_id,
              persona_id: this.persona_id,
              numero: this.numero,
              nombre_banco: this.nombre_banco,
            })
            .then((/*response*/) => (
              //console.log(response)
              this.getCuentas(),
              //Se borra la informacion de las variables
              this.cuenta_id = '',
              this.persona_id = '',
              this.numero = '',
              this.nombre_banco = 'Banco Pichincha'
            ))
            .catch((error) => (console.log(error)))
          } else {
            //indica que va a ingresar una cuenta nueva
            axios.post('./ajaxfile.php', {
              request: 'insertar_cuenta',
              persona_id: this.persona_id,
              numero: this.numero,
              nombre_banco: this.nombre_banco
            })
            .then((response) => (
              this.cuentas.unshift({
                id: response.data,
                nombre: this.personas.find((item)=>{return item.id == this.persona_id}).nombre,
                persona_id: this.persona_id,
                numero: this.numero,
                nombre_banco: this.nombre_banco
              }),
              //Se borra la informacion de las variables
              this.cuenta_id = '',
              this.persona_id = '',
              this.numero = '',
              this.nombre_banco = 'Banco Pichincha'
            ))
            .catch((error) => (console.log(error)))
          }
        } else {
          alert('Faltan datos.');
        }
      },

      getCuentas: function() {
        axios.post('ajaxfile.php', {
          request: 'consulta_cuenta',
         })
         .then(response => (this.cuentas = response.data))
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

    deleteCuenta: function (cuenta) {
      axios.post('./ajaxfile.php', {
        request: 'eliminar_cuenta',
        cuenta_id: cuenta.id
       })
       .then(response => (
         console.log(response),
         this.getCuentas()
       ))
       .catch( (error) => (
         console.log(error)
     ));
    },

    editCuenta: function (cuenta) {
      console.log(cuenta);
      this.persona_id = cuenta.persona_id,
      this.cuenta_id = cuenta.id,
      this.numero = cuenta.numero,
      this.nombre_banco = cuenta.nombre_banco
    },
    },

    data () {
      return {
          persona_id:'',
          cuenta_id:'',
          numero:'',
          nombre_banco:'Banco Pichincha',
          search: '',
          cuentas: [],
          personas: [],
          cuentaRules: [
            v => !!v || 'Numero de cuenta es Obligatorio',
            v => v.length == 10 || 'La cedula debe tener 10 digitos',
          ],
          headers: [
            {
              text: 'Persona Nombre',
              align: 'start',
              value: 'nombre',
            },
            { text: 'Número de Cuenta', value: 'numero' },
            { text: 'Nombre del Banco', value: 'nombre_banco' },
            { text: 'Acciones', value: 'actions', sortable: false }
          ],
          bancos: [
            {id:0, nombre:'Banco Pichincha'},
            {id:1, nombre:'Banco de Machala'},
            {id:2, nombre:'Banco del Pacífico'},
            {id:3, nombre:'Banco Guayaquil'},
            {id:4, nombre:'Banco Bolivariano'},
            {id:5, nombre:'BanEcuador'},
            {id:6, nombre:'Banco de Loja'},
            {id:7, nombre:'Banco Internacional'},
            {id:8, nombre:'Diners Club'},
            {id:9, nombre:'Citibank'},
            {id:10, nombre:'Banco ProCredit'},
            {id:11, nombre:'Banco General Rumiñahui'},
            {id:12, nombre:'Biess'},
            {id:13, nombre:'Produbanco'},
            {id:14, nombre:'Banco Amazonas'},
            {id:15, nombre:'Banco de Desarrollo del Ecuador'},
            {id:16, nombre:'Banco Solidario'},
            {id:17, nombre:'Banco del Austro'},
            {id:18, nombre:'Banco Coopnacional'},
            {id:19, nombre:'Banco VisionFund Ecuador'},
            {id:20, nombre:'Banco del Litoral'},
            {id:21, nombre:'Banco Delbank'},
            {id:22, nombre:'Banco Desarrollo'},
            {id:23, nombre:'Banco Finca'},
            {id:24, nombre:'Banco Comercial de Manabí'},
            {id:25, nombre:'Banco Capital'}
          ],
        }
    },
  };
</script>
