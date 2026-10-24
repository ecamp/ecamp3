<template>
  <Text class="category-label" :style="style">
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
    size: { type: Number, default: 10 },
  },
  computed: {
    style() {
      const verticalPadding = 0.15 * this.size
      const horizontalPadding = 0.65 * this.size
      return {
        color: contrastColor(this.category.color),
        backgroundColor: this.category.color,
        fontSize: this.size,
        letterSpacing: 0.05 * this.size,
        paddingTop: verticalPadding,
        paddingBottom: verticalPadding,
        paddingLeft: horizontalPadding,
        paddingRight: horizontalPadding,
        marginTop: -verticalPadding,
        alignSelf: 'flex-start',
      }
    },
  },
}
</script>
<style lang="react-pdf">
.category-label {
  border-radius: 999pt;
}
</style>
