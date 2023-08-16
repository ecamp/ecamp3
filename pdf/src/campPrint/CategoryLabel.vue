<template>
  <Text class="category-label" :style="{ color, backgroundColor }">
    {{ category.short }}
  </Text>
</template>
<script>
import PdfComponent from '@/PdfComponent.js'
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
<pdf-style>
.category-label {
  padding: 2pt 8pt 4pt;
  border-radius: 50%;
  align-self: center;
  line-height: 1;
}
</pdf-style>
