<template>
  <Svg :width="size" :height="size" viewBox="0 0 755.907 755.907">
    <Path :d="path" class="ys-logo-path" />
  </Svg>
</template>
<script>
import PdfComponent from '@/PdfComponent.js'

export default {
  name: 'YSLogo',
  extends: PdfComponent,
  props: {
    size: { type: Number, default: 20 },
    locale: { type: String, default: 'en' },
  },
  computed: {
    path() {
      if (this.locale && this.locale.match(/^it/i)) {
        // Italian has a different logo (G+S instead of J+S)
        return 'M748 323V191 323H639.3193A200 200 0 01747 503V565A90 94 0 01381 565H433.1321V433.6068H563.2424V323.5098H433.1321V191.047H380.2688A185 210 0 01748 191M322.4024 191V323.5098H190.4173V433.6068H322V565H374V755H175.8372A185 210 0 018 543V191A184 210 0 01373 191Z'
      }
      return 'M747 324H640A140 150 0 01745.7661 466.6977V581.2591A1 1 0 01383 565H431.9795V435.5404H563.7701V324.1804H431.9795V193.2711H381.9101A152 160 0 01747 190V324M373.6341 8.7111V193.2844H321.8635V324.1804H186.8021V8.4524H373.6341M321.5128 564.8657H372.7061A1 1 0 018 581V435.5484H321.5128V564.8657'
    },
  },
}
</script>
<pdf-style>
.ys-logo-path {
  fill: #e92d35;
  fill-rule: evenodd;
  stroke: none;
}
</pdf-style>
