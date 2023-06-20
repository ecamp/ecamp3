<template>
  <pdf-path v-bind="presentProps">
    <slot />
  </pdf-path>
</template>
<script>
import { presentationAttributesMixin } from './presentationAttributesMixin.js'

// https://react-pdf.org/svg#path
export default {
  name: 'Path',
  mixins: [presentationAttributesMixin],
  props: {
    d: { type: String, default: undefined },
  },
}
</script>
