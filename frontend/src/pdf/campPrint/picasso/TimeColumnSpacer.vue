<template>
  <View class="picasso-time-column" :style="{ width }" />
</template>
<script>
import PdfComponent from '@/pdf/PdfComponent.js'
import { timeColumnWidth } from './longestTime.js'

export default {
  name: 'TimeColumnSpacer',
  extends: PdfComponent,
  props: {
    times: { type: Array, required: true },
  },
  computed: {
    width() {
      return timeColumnWidth(this.times, this.$date)
    },
  },
}
</script>
