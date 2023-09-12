<template>
  <Svg class="checkmark" :style="style" height="8" width="8">
    <Circle cx="4" cy="4" r="4" fill="green" />
    <Polygon
      points="3.3,4.725 2.25,3.65 1.6,4.275 3.3,5.975 6.4,2.9 5.75,2.275"
      fill="white"
    />
  </Svg>
</template>
<script>
import PdfComponent from '@/PdfComponent.js'

export default {
  name: 'Checkmark',
  extends: PdfComponent,
  props: {
    size: { type: Number, default: 12 },
  },
  computed: {
    style() {
      return { transform: `scale(${this.size / 8})` }
    },
  },
}
</script>
<pdf-style>
.checkmark {
  margin-top: 1.5pt;
}
</pdf-style>
