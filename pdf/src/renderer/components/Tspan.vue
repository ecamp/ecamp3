<template>
  <pdf-tspan v-bind="presentProps">
    <slot />
  </pdf-tspan>
</template>
<script>
import { presentationAttributesMixin } from './presentationAttributesMixin.js'

// https://react-pdf.org/svg#tspan
export default {
  name: 'Tspan',
  mixins: [presentationAttributesMixin],
  props: {
    x: { type: [String, Number], default: undefined },
    y: { type: [String, Number], default: undefined },
  },
}
</script>
