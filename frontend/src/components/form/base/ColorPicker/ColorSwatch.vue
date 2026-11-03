<template>
  <v-btn
    class="e-colorswatch"
    :class="{ 'e-colorswatch--null': color == null }"
    rounded
    elevation="0"
    width="30"
    height="30"
    :color="color"
    :ripple="false"
    v-bind="$attrs"
    @click="$emit('selectColor', color)"
  ></v-btn>
</template>
<script>
import { contrastColor } from '@/helpers/colors.js'

export default {
  name: 'ColorSwatch',
  props: {
    color: { type: String, default: null },
  },
  emits: ['selectColor'],
  computed: {
    contrast() {
      // Vuetify returns invalid value #NANNAN in the initialization phase
      return this.color && this.color !== '#NANNAN' ? contrastColor(this.color) : 'black'
    },
  },
}
</script>
<style scoped>
.e-colorswatch {
  min-width: 0;
  padding: 0;

  :deep(.v-btn__overlay) {
    background-color: transparent;
  }
}
.e-colorswatch::before {
  background: transparent;
}
.e-colorswatch:focus {
  transform: scale(1.1);
}
.e-colorswatch:focus::after {
  opacity: 1;
  outline: 1px solid v-bind(contrast);
  border: none;
  box-shadow:
    0 10px 15px -3px rgba(0, 0, 0, 0.3),
    0 4px 6px -4px rgba(0, 0, 0, 0.4);
}
.e-colorswatch::before {
  content: '•';
  color: v-bind(contrast);
  display: block;
  width: 100%;
  height: 100%;
  line-height: 26px;
  font-size: 28px;
  text-align: center;
}
.e-colorswatch--null {
  background: #f0f0f0;
  box-shadow:
    inset 0 1px 3px rgba(0, 0, 0, 0.1),
    inset 0 0 10px rgba(0, 0, 0, 0.02) !important;
}
</style>
