<template>
  <View class="picasso-time-column picasso-time-column-spacer">
    <Text class="picasso-time-column-text picasso-time-column-spacer-text">{{
      longestTime
    }}</Text>
  </View>
</template>
<script>
import PdfComponent from '@/PdfComponent.js'
import longestTime from './longestTime.js'

export default {
  name: 'TimeColumnSpacer',
  extends: PdfComponent,
  props: {
    times: { type: Array, required: true },
  },
  computed: {
    longestTime() {
      return longestTime(this.times, this.$date)
    },
  },
}
</script>
<pdf-style>
.picasso-time-column-spacer {
  margin-top: 0;
  margin-bottom: 0;
}
.picasso-time-column-spacer-text {
  opacity: 0;
}
</pdf-style>
