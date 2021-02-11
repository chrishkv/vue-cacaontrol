<template>
  <div>
  <v-tabs
    color="cyan"
    dark
    slider-color="yellow"
    v-model="active_tab"
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
    <v-tab-item>
        <Ordenes/>
    </v-tab-item>
    <v-tab-item>
      <v-card flat>
        <v-card-text>Mostrando Otras Ordenes</v-card-text>
      </v-card>
    </v-tab-item>
    <v-tab-item>
      <v-card flat>
        <Personas/>
      </v-card>
    </v-tab-item>
    <v-tab-item>
      <v-card flat>
        <v-card-text>Mostrando Cuentas</v-card-text>
      </v-card>
    </v-tab-item>
    </v-tabs>
  </div>
</template>

<script>
import Ordenes from './Ordenes.vue';
import Personas from './Personas.vue';

export default {
  name: 'Principal',

  components: {
    Ordenes,
    Personas,
  },

  data: () => ({
    active_tab: 2,
  }),
};
</script>
