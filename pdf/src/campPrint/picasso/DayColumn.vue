<template>
  <View class="picasso-day-column">
    <View class="picasso-day-column-grid">
      <View
        v-for="([_, weight], index) in times"
        class="picasso-day-column-grid-row"
        :class="{ 'picasso-day-column-grid-row-grey': index % 2 === 1 }"
        :style="{ flexGrow: weight }"
      ></View>
    </View>
    <View class="picasso-day-column-schedule-entry-container">
      <ScheduleEntry
        v-for="scheduleEntry in relevantScheduleEntries"
        :schedule-entry="scheduleEntry"
        :style="{
          ...positionStyles[scheduleEntry.id],
          ...borderRadiusStyles[scheduleEntry.id],
        }"
        :percentage-height="positionStyles[scheduleEntry.id].percentageHeight"
      />
    </View>
  </View>
</template>
<script>
import PdfComponent from '@/PdfComponent.js'
import ScheduleEntry from './ScheduleEntry.vue'
import {
  filterScheduleEntriesByDay,
  dayStart,
  dayEnd,
  positionStyles,
} from '../../../common/helpers/picasso.js'

import keyBy from 'lodash/keyBy.js'

export default {
  name: 'DayColumn',
  components: { ScheduleEntry },
  extends: PdfComponent,
  props: {
    times: { type: Array, required: true },
    day: { type: Object, required: true },
    scheduleEntries: { type: Array, default: () => [] },
  },
  computed: {
    relevantScheduleEntries() {
      return filterScheduleEntriesByDay(this.scheduleEntries, this.day, this.times)
    },
    positionStyles() {
      return positionStyles(this.relevantScheduleEntries, this.day, this.times)
    },
    borderRadiusStyles() {
      const radius = '2pt'
      return keyBy(
        this.relevantScheduleEntries.map((scheduleEntry) => {
          const start = this.$date.utc(scheduleEntry.start)
          const startsOnThisDay =
            start.isSameOrAfter(dayStart(this.day, this.times)) &&
            start.isSameOrBefore(dayEnd(this.day, this.times))
          const topStyles = startsOnThisDay
            ? { borderTopRightRadius: radius, borderTopLeftRadius: radius }
            : {}

          const end = this.$date.utc(scheduleEntry.end)
          const endsOnThisDay =
            end.isSameOrAfter(dayStart(this.day, this.times)) &&
            end.isSameOrBefore(dayEnd(this.day, this.times))
          const bottomStyles = endsOnThisDay
            ? { borderBottomRightRadius: radius, borderBottomLeftRadius: radius }
            : {}

          return { id: scheduleEntry.id, ...bottomStyles, ...topStyles }
        }),
        'id'
      )
    },
  },
}
</script>
<pdf-style>
.picasso-day-column {
  flex-basis: 0;
  flex-grow: 1;
  display: flex;
  flex-direction: column;
  overflow: hidden;
  position: relative;
}
.picasso-day-column-grid {
  width: 100%;
  height: 100%;
  display: flex;
  flex-direction: column;
  overflow: hidden;
  position: relative;
}
.picasso-day-column-grid-row {
  display: flex;
  flex-basis: 0;
}
.picasso-day-column-grid-row-grey {
  background-color: lightgrey;
}
.picasso-day-column-schedule-entry-container {
  margin: 0 2pt;
  position: absolute;
  top: 0;
  bottom: 0;
  left: 0;
  right: 0;
}
</pdf-style>
