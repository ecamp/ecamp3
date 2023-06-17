<template>
  <pdf-rect v-bind="$props">
    <slot />
  </pdf-rect>
</template>
<script>
import { presentationAttributesMixin } from './presentationAttributesMixin.js'

// https://react-pdf.org/svg#rect
export default {
  name: 'Rect',
  mixins: [presentationAttributesMixin],
  props: {
    x: { type: [String, Number], default: undefined },
    y: { type: [String, Number], default: undefined },
    width: { type: [String, Number], default: undefined },
    height: { type: [String, Number], default: undefined },
    rx: { type: [String, Number], default: undefined },
    ry: { type: [String, Number], default: undefined },
  },
}
</script>
