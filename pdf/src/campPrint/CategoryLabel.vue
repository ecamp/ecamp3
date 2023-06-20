<template>
  <Text class="category-label" :style="{ color, backgroundColor }">
    {{ category.short }}
  </Text>
</template>
<script>
import PdfComponent from '@/PdfComponent.js'
import { contrastColor } from '@/common/helpers/colors.js'

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
<pdf-style>
.category-label {
  padding: 0 8pt 2pt;
  border-radius: 50%;
  margin-bottom: 0;
  align-self: center;
}
</pdf-style>
