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
      Reportes
    </v-tab>
    <v-tab-item>
        <Ordenes/>
    </v-tab-item>
    <v-tab-item>
      <v-card flat>
        <OtrasCuentas/>
      </v-card>
    </v-tab-item>
    <v-tab-item>
      <v-card flat>
        <Personas/>
      </v-card>
    </v-tab-item>
    <v-tab-item>
      <v-card flat>
        <Cuentas/>
      </v-card>
    </v-tab-item>
    <v-tab-item>
      <v-card flat>
        <Reportes/>
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

export default {
  name: 'Principal',

  components: {
    Ordenes,
    Personas,
    Cuentas,
    OtrasCuentas,
    Reportes
  },

  data: () => ({
  }),
};
</script>
