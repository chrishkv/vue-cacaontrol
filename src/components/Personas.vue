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
          <v-text-field v-model="mail" filled label="E-Mail" :rules="emailRules"></v-text-field>
        </v-col>

        <v-col cols="12" sm="4">
          <v-text-field v-model="direccion" filled label="Direccion"></v-text-field>
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
          label="Search"
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
          @click="editItem(item)"
        >
          mdi-pencil
        </v-icon>
        <v-icon
          small
          @click="deleteItem(item)"
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
  export default {
    mounted() {
      this.getPersonas();
    },

    methods: {
      addPersona: function () {
        if(this.nombre != '' && this.cedula != '') {
          axios.post('./ajaxfile.php', {
            request: 'insertar_persona',
            nombre: this.nombre,
            cedula: this.cedula,
            mail: this.mail,
            direccion: this.direccion
          })
          .then((/*response*/) => (
            //console.log(response)
            this.personas.unshift({
              nombre: this.nombre,
              cedula: this.cedula,
              mail: this.mail,
              direccion: this.direccion
            })
          ))
          .catch(error => (console.log(error)))
        } else {
          alert('Faltan datos.');
        }
      },

      getPersonas: function() {
        axios.post('./ajaxfile.php', {
          request: 'consulta_personas',
         })
         .then(response => (this.personas = response.data))
         .catch(() => (//console.log(error)
           this.personas = [
             {
               nombre: 'Primero',
               cedula: 12354,
               mail: 'inventado',
               direccion: 'otra mas',
             },
             {
               nombre: 'Segundo',
               cedula: 12354,
               mail: 'mucho otro',
               direccion: 'otra menos',
             },
             {
               nombre: 'Tercero',
               cedula: 12354,
               mail: 'inventado otro',
               direccion: 'mas otra ',
             }]
       ));
     },
    },

    data () {
      return {
          persona_id:'',
          nombre:'',
          cedula:'',
          mail:'',
          direccion:'',
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
          headers: [
            {
              text: 'Nombre y Apellido',
              align: 'start',
              value: 'nombre',
            },
            { text: 'Cedula', value: 'cedula' },
            { text: 'E-mail', value: 'mail' },
            { text: 'Direccion', value: 'direccion' },
            { text: 'Acciones', value: 'actions', sortable: false }
          ],
        }
    },
  };
</script>
