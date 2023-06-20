<template>
  <pdf-g v-bind="presentProps">
    <slot />
  </pdf-g>
</template>
<script>
import { presentationAttributesMixin } from './presentationAttributesMixin.js'

// https://react-pdf.org/svg#g
export default {
  name: 'G',
  mixins: [presentationAttributesMixin],
}
</script>
