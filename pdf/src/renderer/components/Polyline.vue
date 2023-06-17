<template>
  <pdf-polyline v-bind="$props">
    <slot />
  </pdf-polyline>
</template>
<script>
import { presentationAttributesMixin } from './presentationAttributesMixin.js'

// https://react-pdf.org/svg#polyline
export default {
  name: 'Polyline',
  mixins: [presentationAttributesMixin],
  props: {
    points: { type: String, default: undefined },
  },
}
</script>
