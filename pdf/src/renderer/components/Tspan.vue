<template>
  <pdf-tspan v-bind="$props">
    <slot />
  </pdf-tspan>
</template>
<script>
import { presentationAttributesMixin } from './presentationAttributesMixin.js'

// https://react-pdf.org/svg#tspan
export default {
  name: 'Text',
  mixins: [presentationAttributesMixin],
  props: {
    x: { type: [String, Number], default: undefined },
    y: { type: [String, Number], default: undefined },
  },
}
</script>
