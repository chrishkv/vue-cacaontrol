<template>
  <div>
  <v-tabs
    color="black"
    background-color="#4DB6AC"
    slider-color="yellow"
    >
    <v-tab ripple>
      Ordenes
    </v-tab>
    <v-tab ripple>
      Otras Ordenes
    </v-tab>
    <v-tab ripple>
      Personas
    </v-tab>
    <v-tab ripple>
      Cuentas
    </v-tab>
    <v-tab ripple>
      Facturas
    </v-tab>
    <v-tab ripple>
      Reportes
    </v-tab>
    <v-tab-item>
        <Ordenes :personas="persons"/>
    </v-tab-item>
    <v-tab-item>
      <v-card flat>
        <OtrasCuentas :personas="persons"/>
      </v-card>
    </v-tab-item>
    <v-tab-item>
      <v-card flat>
        <Personas :personas="persons"
                  @deletePerson="deletePerson"
                  @agregarPerson="agregarPerson"
                  @editarPerson="editarPerson"/>
      </v-card>
    </v-tab-item>
    <v-tab-item>
      <v-card flat>
        <Cuentas :personas="persons"/>
      </v-card>
    </v-tab-item>
    <v-tab-item>
      <v-card flat>
        <Facturas :personas="persons"/>
      </v-card>
    </v-tab-item>
    <v-tab-item>
      <v-card flat>
        <Reportes :personas="persons"/>
      </v-card>
    </v-tab-item>
    </v-tabs>
  </div>
</template>

<script>
import Ordenes from './Ordenes.vue';
import Personas from './Personas.vue';
import Cuentas from './Cuentas.vue';
import OtrasCuentas from './OtrasCuentas.vue';
import Reportes from './Reportes.vue';
import Facturas from './Facturas.vue';
import axios from 'axios'

export default {
  name: 'Principal',

  components: {
    Ordenes,
    Personas,
    Cuentas,
    OtrasCuentas,
    Reportes,
    Facturas
  },

  mounted() {
    this.getPersonas()
  },

  methods: {
    getPersonas () {
      axios.post('./ajaxfile.php', {
        request: 'consulta_personas_select',
       })
       .then(response => (this.persons = response.data))
       .catch((error) => (console.log(error)));
   },

   deletePerson (persona_id) {
     let indice = this.persons.findIndex(item => item.id === persona_id)
     this.persons.splice(indice, 1)
   },

   agregarPerson (Persona) { this.persons.unshift(Persona) },

   editarPerson (Persona) {
     let indice = this.persons.findIndex(item => item.id === Persona.id)
     this.persons.splice(indice, 1, Persona)
   },
  },

  data () {
    return {
      persons: [],
    }
  },
};
</script>
