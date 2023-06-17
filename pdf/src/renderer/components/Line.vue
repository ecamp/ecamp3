<template>
  <pdf-line v-bind="$props">
    <slot />
  </pdf-line>
</template>
<script>
import { presentationAttributesMixin } from './presentationAttributesMixin.js'

// https://react-pdf.org/svg#line
export default {
  name: 'Line',
  mixins: [presentationAttributesMixin],
  props: {
    x1: { type: [String, Number], default: undefined },
    x2: { type: [String, Number], default: undefined },
    y1: { type: [String, Number], default: undefined },
    y2: { type: [String, Number], default: undefined },
  },
}
</script>
