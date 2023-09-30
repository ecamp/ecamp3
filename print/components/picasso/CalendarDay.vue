<template>
  <div class="v-calendar-daily__day v-future">
    <!-- background interval shades -->
    <div
      v-for="(time, i) in displayedTimes"
      :key="i"
      class="v-calendar-daily__day-interval"
      :style="`height: ${time.height}px`"
    ></div>

    <div class="v-event-timed-container">
      <div
        v-for="scheduleEntry in relevantScheduleEntries"
        :key="scheduleEntry.id"
        class="v-event-timed black--text"
        :style="{
          ...positionStyles[scheduleEntry.id],
          ...borderRadiusStyles[scheduleEntry.id],
          ...colorStyles[scheduleEntry.id],
        }"
      >
        <PicassoScheduleEntry :schedule-entry="scheduleEntry" />
        <span class="tw-float-right tw-italic ml-1" style="color: #000"> </span>
      </div>
    </div>
  </div>
</template>

<script>
import { keyBy } from 'lodash'
import { arrange } from './scheduleEntryLayout.js'

export default {
  props: {
    times: { type: Array, required: true },
    displayedTimes: { type: Array, required: true },
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
            percentageHeight: 100 - bottom - top,
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
    colorStyles() {
      return keyBy(
        this.relevantScheduleEntries.map((scheduleEntry) => {
          const color = this.getActivityColor(scheduleEntry)
          return {
            id: scheduleEntry.id,
            'background-color': color,
            'border-color': color,
          }
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
    getActivityColor(scheduleEntry) {
      return scheduleEntry.activity().category().color
    },
  },
}
</script>
