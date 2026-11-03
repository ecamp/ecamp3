<template>
  <Text class="category-label" :style="style" :wrap="false">
    {{ category.short }}
  </Text>
</template>
<script>
import PdfComponent from '@/pdf/PdfComponent.js'
import { contrastColor } from '../../common/helpers/colors.js'

const DEFAULT_TEXT_FACTOR = 0.8
export default {
  name: 'CategoryLabel',
  extends: PdfComponent,
  props: {
    category: { type: Object, required: true },
    size: { type: Number, default: null },
    lineFontSize: { type: Number, required: true },
  },
  computed: {
    factor() {
      return this.size != null ? this.size / this.lineFontSize : DEFAULT_TEXT_FACTOR
    },
    style() {
      const size = this.size ?? this.lineFontSize * this.factor
      const verticalPadding = 0.15 * size
      const horizontalPadding = 0.65 * size
      const lineHeightFactor = 1.21
      return {
        color: contrastColor(this.category.color),
        backgroundColor: this.category.color,
        fontSize: size,
        fontWeight: 700,
        letterSpacing: 0.05 * size,
        paddingTop: verticalPadding,
        paddingBottom: verticalPadding,
        paddingLeft: horizontalPadding,
        paddingRight: horizontalPadding,
        marginTop: (lineHeightFactor * (this.lineFontSize - size)) / 2 - verticalPadding,
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
