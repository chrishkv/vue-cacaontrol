<template>
  <div>
  <br>
  <v-form>
    <v-container>
      <v-row>
        <v-col cols="12" sm="3">
          <v-text-field v-model="nombre" filled label="Nombre y Apellido" :rules="nameRules" clearable required></v-text-field>
        </v-col>

        <v-col cols="12" sm="2">
          <v-text-field v-model="cedula" filled label="Cedula" :rules="cedulaRules" required></v-text-field>
        </v-col>

        <v-col cols="12" sm="2">
          <v-text-field v-model="telefono" filled label="Telefono" :rules="telefonoRules"></v-text-field>
        </v-col>

        <v-col cols="12" sm="2">
          <v-text-field v-model="mail" filled label="E-Mail" :rules="emailRules"></v-text-field>
        </v-col>

        <v-col cols="12" sm="2">
          <v-text-field v-model="hectarea" filled label="Hectarea" :rules="hectareaRules" required></v-text-field>
        </v-col>

        <v-col cols="12" sm="5">
          <v-text-field v-model="direccion" filled label="Direccion"></v-text-field>
        </v-col>

        <v-col cols="12" sm="5">
          <v-text-field v-model="parcela" filled label="Direccion de Parcela"></v-text-field>
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
            .then((/*response*/) => (
              //console.log(response),
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
            ))
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
            .then((response) => (
              //Agrega a la persona en la lista en la primera posición
              console.log(response),
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
            ))
            .catch((error) => (console.log(error)))
          }
        } else {
          alert('Faltan datos revise los campos en rojo.');
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
       alert('funcion aun no disponible')
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
