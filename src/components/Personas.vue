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
      <v-row align="center" justify="center">
        <v-col cols="12" sm="3">
          <v-text-field v-model="nombre" filled label="Nombre y Apellido" :rules="nameRules" clearable background-color="#AFEEEE" required></v-text-field>
        </v-col>

        <v-col cols="12" sm="2">
          <v-text-field v-model="cedula" filled label="Cedula" :rules="cedulaRules" background-color="#AFEEEE" required></v-text-field>
        </v-col>

        <v-col cols="12" sm="2">
          <v-text-field v-model="telefono" filled label="Telefono" :rules="telefonoRules" background-color="#AFEEEE"></v-text-field>
        </v-col>

        <v-col cols="12" sm="2">
          <v-text-field v-model="mail" filled label="E-Mail" :rules="emailRules" background-color="#AFEEEE"></v-text-field>
        </v-col>

        <v-col cols="12" sm="2">
          <v-text-field v-model="hectarea" filled label="Hectarea" :rules="hectareaRules" background-color="#AFEEEE" required></v-text-field>
        </v-col>

        <v-col cols="12" sm="5">
          <v-text-field v-model="direccion" filled label="Direccion" background-color="#AFEEEE"></v-text-field>
        </v-col>

        <v-col cols="12" sm="5">
          <v-text-field v-model="parcela" filled label="Direccion de Parcela" background-color="#AFEEEE"></v-text-field>
        </v-col>

        <v-col cols="12" sm="1">
          <v-btn v-on:click="addPersona" color="primary" dark large>Guardar</v-btn>
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
        :items="personas"
        :search="search"
        :footer-props="{
          itemsPerPageText: 'Filas por página:',
          itemsPerPageAllText: 'Todos',
          itemsPerPageOptions: [20,50,100,-1],
        }"
      >
      <template v-slot:item.actions="{ item }">
        <v-icon
          small
          class="mr-2"
          @click="editPersona(item)"
        >
          mdi-pencil
        </v-icon>
        <v-icon
          small
          @click="deletePersona(item)"
        >
          mdi-delete
        </v-icon>
      </template>
      <template #footer.page-text="props">
        {{props.pageStart}}-{{props.pageStop}} de ~{{props.itemsLength}}
      </template>
      </v-data-table>
    </v-card>
  </div>
</template>
<script>
import axios from 'axios'
import EventBus from '../bus'
  export default {
    mounted() {
      this.getPersonas();
    },

    methods: {
      addPersona: function () {
        if(this.nombre != '' && this.cedula != '') {
          //indica que va a editar
          if (this.persona_id) {
            axios.post('./ajaxfile.php', {
              request: 'editar_persona',
              persona_id: this.persona_id,
              nombre: this.nombre,
              cedula: this.cedula,
              telefono: this.telefono,
              mail: this.mail,
              hectarea: this.hectarea,
              direccion: this.direccion,
              parcela: this.parcela
            })
            .then((response) => {
              if (response.data) {
                this.SnackbarShow("success", "Editado Correctamente."),
                //Se borra la informacion de las variables
                this.persona_id = '',
                this.nombre = '',
                this.cedula = '',
                this.telefono = '',
                this.mail = '',
                this.hectarea = '',
                this.direccion = '',
                this.parcela = '',
                //luego de editar a la persona vuelve a rehacer la lista desde la BD
                this.getPersonas()
              } else {
                this.SnackbarShow("error", "Hubo un Error al guardar, disculpe.")
              }
            })
            .catch((error) => (console.log(error)))
          } else {
            //indica que va a ingresar una persona nueva
            axios.post('./ajaxfile.php', {
              request: 'insertar_persona',
              nombre: this.nombre,
              cedula: this.cedula,
              telefono: this.telefono,
              mail: this.mail,
              hectarea: this.hectarea,
              direccion: this.direccion,
              parcela: this.parcela
            })
            .then((response) => {
              if (response.data) {
                this.SnackbarShow("success", "Guardado Correctamente."),
                //Agrega a la persona en la lista en la primera posición
                this.personas.unshift({
                  id: response.data,
                  nombre: this.nombre,
                  cedula: this.cedula,
                  telefono: this.telefono,
                  mail: this.mail,
                  hectarea: this.hectarea,
                  direccion: this.direccion,
                  parcela: this.parcela
                }),
                EventBus.$emit('add-persona', [this.nombre, response.data]),
                //Se borra la informacion de las variables
                this.persona_id = '',
                this.nombre = '',
                this.cedula = '',
                this.telefono = '',
                this.mail = '',
                this.hectarea = '',
                this.direccion = '',
                this.parcela = ''
            } else {
              this.SnackbarShow("error", "Hubo un Error al guardar, disculpe.")
            }
          })
            .catch((error) => (console.log(error)))
          }
        } else {
          this.SnackbarShow("warning", "Faltan algunos datos por favor revise.")
        }
      },

      getPersonas: function() {
        axios.post('./ajaxfile.php', {
          request: 'consulta_personas',
         })
         .then(response => (this.personas = response.data))
         .catch((error) => (console.log(error)
       ));
     },

     editPersona(persona) {
       this.persona_id = persona.id,
       this.nombre = persona.nombre,
       this.cedula = persona.cedula,
       this.telefono = persona.telefono,
       this.mail = persona.mail,
       this.hectarea = persona.hectarea,
       this.direccion = persona.direccion,
       this.parcela = persona.parcela
     },

     deletePersona(/*persona*/) {
       this.SnackbarShow("error", "Funcion aún en construcción")
       /*axios.post('./ajaxfile.php', {
         request: 'eliminar_persona',
         persona_id: persona.persona_id,
       })
       .then((response) => (
         this.personas.unshift({
           nombre: this.nombre,
           cedula: this.cedula,
           telefono: this.telefono,
           mail: this.mail,
           direccion: this.direccion
         }),
         EventBus.$emit('remove-persona', [this.nombre, response.data])
       ))
       .catch(error => (console.log(error)))*/
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
          persona_id:'',
          nombre:'',
          cedula:'',
          telefono:'',
          mail:'',
          hectarea:'',
          direccion:'',
          parcela:'',
          search: '',
          personas: [],
          snackbar: {
            color: null,
            text: null,
            timeout: 2500,
            title: null,
            visible: false
          },
          nameRules: [
            v => !!v || 'Nombre es Obligatorio',
            v => v.length >= 10 || 'Nombre debe ser mayor a 10 Caracteres',
          ],
          cedulaRules: [
            v => !!v || 'Cedula es Obligatorio',
            v => v.length == 10 || 'La cedula debe tener 10 digitos',
          ],
          emailRules: [
            v => /^\w+([.-]?\w+)*@\w+([.-]?\w+)*(\.\w{2,3})+$/.test(v) || 'E-mail debe ser valido',
          ],
          telefonoRules: [
            v => v.length > 8 || 'El telefono debe tener mas de 8 digitos',
          ],
          hectareaRules: [
            v => v > 0 || 'El valor debe ser mayor a 0',
          ],
          headers: [
            {
              text: 'Nombre y Apellido',
              align: 'start',
              value: 'nombre',
            },
            { text: 'Cedula', value: 'cedula' },
            { text: 'Telefono', value: 'telefono' },
            { text: 'E-mail', value: 'mail' },
            { text: 'Hectarea', value: 'hectarea' },
            { text: 'Direccion', value: 'direccion' },
            { text: 'Parcela', value: 'parcela' },
            { text: 'Acciones', value: 'actions', sortable: false }
          ],
        }
    },
  };
</script>
