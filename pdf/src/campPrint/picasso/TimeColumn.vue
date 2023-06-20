<template>
  <View class="picasso-time-column" :style="{ width }">
    <View class="picasso-time-column-container">
      <View
        v-for="{ time, weight } in displayedTimes"
        :key="time"
        class="picasso-time-column-row"
        :style="{
          flexGrow: weight,
          alignItems: align === 'left' ? 'flex-start' : 'flex-end',
        }"
      >
        <Text class="picasso-time-column-text">{{ time }}</Text>
      </View>
    </View>
  </View>
</template>
<script>
import PdfComponent from '@/PdfComponent.js'
import longestTime from './longestTime.js'

export default {
  name: 'TimeColumn',
  extends: PdfComponent,
  props: {
    times: { type: Array, required: true },
    align: { type: String, default: 'left' },
  },
  computed: {
    width() {
      // we could do better than this heuristic...
      return 5 * longestTime(this.times, this.$date).length
    },
    displayedTimes() {
      return this.times.map(([time, weight], index) => {
        if (index === 0) return { time: ' ', weight }
        return {
          weight,
          time: this.$date()
            .hour(0)
            .minute(time * 60)
            .second(0)
            .format('LT'),
        }
      })
    },
  },
}
</script>
<pdf-style>
.picasso-time-column-container {
  /*
   Wrapping the time column in this absolutely positioned View is necessary, because otherwise the text
   in the time column breaks layouting of the texts inside the schedule entries.
   */
  position: absolute;
  top: -6;
  bottom: 6;
  left: 0;
  right: 0;
}
.picasso-time-column-row {
  padding-horizontal: 2pt;
  /* this should match the height of the borders on the day grid rows. 0 means no borders */
  flex-basis: 0;
}
</pdf-style>
