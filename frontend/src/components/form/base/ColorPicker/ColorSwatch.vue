<template>
  <v-btn
    class="e-colorswatch"
    fab
    elevation="0"
    width="30"
    height="30"
    :color="color"
    :ripple="false"
    @click="$emit('select-color', color)"
    v-on="$listeners"
  ></v-btn>
</template>
<script>
import { contrastColor } from '@/common/helpers/colors.js'

export default {
  name: 'ColorSwatch',
  props: {
    color: { type: String, required: true },
  },
  computed: {
    contrast() {
      // Vuetify returns invalid value #NANNAN in the initialization phase
      return this.color && this.color !== '#NANNAN' ? contrastColor(this.color) : 'black'
    },
  },
}
</script>
<style scoped>
.e-colorswatch::after {
  content: '•';
  color: v-bind(contrast);
  display: block;
  width: 100%;
  height: 100%;
  line-height: 26px;
  font-size: 28px;
  text-align: center;
}
</style>
