<template>
  <pdf-polygon v-bind="presentProps">
    <slot />
  </pdf-polygon>
</template>
<script>
import { presentationAttributesMixin } from './presentationAttributesMixin.js'

// https://react-pdf.org/svg#polygon
export default {
  name: 'Polygon',
  mixins: [presentationAttributesMixin],
  props: {
    points: { type: String, default: undefined },
  },
}
</script>
