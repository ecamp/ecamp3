<template>
  <pdf-ellipse v-bind="presentProps">
    <slot />
  </pdf-ellipse>
</template>
<script>
// https://react-pdf.org/svg#ellipse
import { presentationAttributesMixin } from './presentationAttributesMixin.js'

// https://react-pdf.org/svg#ellipse
export default {
  name: 'Ellipse',
  mixins: [presentationAttributesMixin],
  props: {
    cx: { type: [String, Number], default: undefined },
    cy: { type: [String, Number], default: undefined },
    rx: { type: [String, Number], default: undefined },
    ry: { type: [String, Number], default: undefined },
  },
}
</script>
