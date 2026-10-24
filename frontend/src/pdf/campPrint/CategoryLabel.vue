<template>
  <Text class="category-label" :style="{ color, backgroundColor }">
    {{ category.short }}
  </Text>
</template>
<script>
import PdfComponent from '@/pdf/PdfComponent.js'
import { contrastColor } from '../../common/helpers/colors.js'

export default {
  name: 'CategoryLabel',
  extends: PdfComponent,
  props: {
    category: { type: Object, required: true },
  },
  computed: {
    backgroundColor() {
      return this.category.color
    },
    color() {
      return contrastColor(this.backgroundColor)
    },
  },
}
</script>
<style lang="react-pdf">
.category-label {
  padding: 2pt 8pt;
  border-radius: 18pt;
  align-self: center;
}
</style>
