<template>
  <v-btn
    class="e-colorswatch"
    fab
    elevation="0"
    width="30"
    height="30"
    :color="color"
    :ripple="false"
    v-bind="$attrs"
    @click="$emit('selectColor', color)"
    v-on="$listeners"
  ></v-btn>
</template>
<script>
import { contrastColor } from '@/common/helpers/colors.js'

export default {
  name: 'ColorSwatch',
  props: {
    color: { type: String, default: null },
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
.e-colorswatch::before {
  background: transparent;
}
.e-colorswatch:focus {
  transform: scale(1.1);
}
.e-colorswatch:focus::before {
  opacity: 1;
  outline: 1px solid v-bind(contrast);
  box-shadow:
    0 10px 15px -3px rgb(0 0 0 / 0.3),
    0 4px 6px -4px rgb(0 0 0 / 0.4);
}
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
