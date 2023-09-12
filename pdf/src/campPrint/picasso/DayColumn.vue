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
      />
    </View>
  </View>
</template>
<script>
import PdfComponent from '@/PdfComponent.js'
import ScheduleEntry from './ScheduleEntry.vue'
import { arrange } from './scheduleEntryLayout.js'
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
    dayBoundariesInMinutes() {
      const dayStart = this.times[0][0]
      const dayEnd = this.times[this.times.length - 1][0]
      const dayStartTimestamp = this.stringToTimestamp(this.day.start)

      return {
        dayStart: dayStartTimestamp + dayStart * 3600,
        dayEnd: dayStartTimestamp + dayEnd * 3600,
      }
    },
    dayStart() {
      return this.dayBoundariesInMinutes.dayStart
    },
    dayEnd() {
      return this.dayBoundariesInMinutes.dayEnd
    },
    relevantScheduleEntries() {
      return this.scheduleEntries.filter((scheduleEntry) => {
        return (
          this.stringToTimestamp(scheduleEntry.start) < this.dayEnd &&
          this.stringToTimestamp(scheduleEntry.end) > this.dayStart
        )
      })
    },
    leftAndWidth() {
      return keyBy(arrange(this.relevantScheduleEntries), 'id')
    },
    positionStyles() {
      return keyBy(
        this.relevantScheduleEntries.map((scheduleEntry) => {
          const left = this.leftAndWidth[scheduleEntry.id]?.left || 0
          const width = this.leftAndWidth[scheduleEntry.id]?.width || 0
          const top = this.percentage(
            this.stringToTimestamp(scheduleEntry.start) -
              this.stringToTimestamp(this.day.start),
            this.times
          )
          const bottom =
            100 -
            this.percentage(
              this.stringToTimestamp(scheduleEntry.end) -
                this.stringToTimestamp(this.day.start),
              this.times
            )
          return {
            id: scheduleEntry.id,
            top: top + '%',
            bottom: bottom + '%',
            left: left + '%',
            right: 100 - width - left + '%',
          }
        }),
        'id'
      )
    },
    borderRadiusStyles() {
      const radius = '2pt'
      return keyBy(
        this.relevantScheduleEntries.map((scheduleEntry) => {
          const start = this.stringToTimestamp(scheduleEntry.start)
          const startsOnThisDay = start >= this.dayStart && start <= this.dayEnd
          const topStyles = startsOnThisDay
            ? { borderTopRightRadius: radius, borderTopLeftRadius: radius }
            : {}

          const end = this.stringToTimestamp(scheduleEntry.end)
          const endsOnThisDay = end >= this.dayStart && end <= this.dayEnd
          const bottomStyles = endsOnThisDay
            ? { borderBottomRightRadius: radius, borderBottomLeftRadius: radius }
            : {}

          return { id: scheduleEntry.id, ...bottomStyles, ...topStyles }
        }),
        'id'
      )
    },
  },
  methods: {
    stringToTimestamp(string) {
      return this.$date.utc(string).unix()
    },
    percentage(seconds) {
      const hours = seconds / 3600.0
      let matchingTimeIndex = this.times.findIndex(([time, _]) => time > hours) - 1
      matchingTimeIndex = Math.min(
        Math.max(matchingTimeIndex === -2 ? this.times.length : matchingTimeIndex, 0),
        this.times.length - 1
      )
      const remainder =
        this.times[matchingTimeIndex][1] !== 0
          ? (hours - this.times[matchingTimeIndex][0]) / this.times[matchingTimeIndex][1]
          : 0 // avoid division by zero, in case the schedule entry ends on a later day
      const positionWeightsSum =
        this.weightsSum(this.times.slice(0, matchingTimeIndex)) +
        remainder * this.times[Math.min(matchingTimeIndex, this.times.length)][1]
      const totalWeightsSum = this.weightsSum(this.times)
      if (totalWeightsSum === 0) {
        return 0
      }
      const result = (positionWeightsSum * 100.0) / totalWeightsSum
      return Math.max(0, Math.min(100, result))
    },
    weightsSum(times) {
      return times.reduce((sum, [_, weight]) => sum + weight, 0)
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
