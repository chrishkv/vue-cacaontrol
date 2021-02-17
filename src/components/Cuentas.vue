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
          <v-text-field v-model="numero" filled type="number" label="Número de Cuenta" :rules="cuentaRules" required></v-text-field>
        </v-col>

        <v-col cols="12" sm="4">
          <v-text-field v-model="nombre_banco" filled label="Nombre del Banco" :rules="nombreBancoRules" required></v-text-field>
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
        if(this.persona_id != '' && this.numero != '' && this.nombre_banco != '') {
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
              this.nombre_banco = ''
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
                cuenta_id: response.data,
                nombre: this.personas.find((item)=>{return item.id == this.persona_id}).nombre,
                persona_id: this.persona_id,
                numero: this.numero,
                nombre_banco: this.nombre_banco
              }),
              //Se borra la informacion de las variables
              this.cuenta_id = '',
              this.persona_id = '',
              this.numero = '',
              this.nombre_banco = ''
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
          nombre_banco:'',
          search: '',
          cuentas: [],
          personas: [],
          cuentaRules: [
            v => !!v || 'Numero de cuenta es Obligatorio',
            v => v.length == 10 || 'La cedula debe tener 10 digitos',
          ],
          nombreBancoRules: [
            v => !!v || 'Nombre es Obligatorio',
            v => v.length >= 10 || 'Nombre debe ser mayor a 10 Caracteres',
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
        }
    },
  };
</script>
