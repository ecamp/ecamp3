<template>
  <pdf-circle v-bind="$props">
    <slot />
  </pdf-circle>
</template>
<script>
import { presentationAttributesMixin } from './presentationAttributesMixin.js'

// https://react-pdf.org/svg#circle
export default {
  name: 'Circle',
  mixins: [presentationAttributesMixin],
  props: {
    cx: { type: [String, Number], default: undefined },
    cy: { type: [String, Number], default: undefined },
    r: { type: [String, Number], default: undefined },
  },
}
</script>
