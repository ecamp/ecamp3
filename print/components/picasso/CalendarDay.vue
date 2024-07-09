<template>
  <div class="v-calendar-daily__day v-future tw-flex tw-flex-col">
    <!-- background interval shades -->
    <div
      v-for="(time, i) in displayedTimes"
      :key="i"
      class="v-calendar-daily__day-interval"
      :style="`flex-grow: ${time.weight}`"
    />

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
        <span class="tw-float-right tw-italic ml-1" style="color: #000" />
      </div>
    </div>
  </div>
</template>

<script>
import keyBy from 'lodash/keyBy.js'

import {
  filterScheduleEntriesByDay,
  positionStyles,
  dayStart,
  dayEnd,
} from '@/common/helpers/picasso.js'

import dayjs from '@/common/helpers/dayjs'

export default {
  props: {
    times: { type: Array, required: true },
    displayedTimes: { type: Array, required: true },
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
          const start = dayjs.utc(scheduleEntry.start)
          const startsOnThisDay =
            start.isSameOrAfter(dayStart(this.day, this.times)) &&
            start.isSameOrBefore(dayEnd(this.day, this.times))
          const topStyles = startsOnThisDay
            ? { borderTopRightRadius: radius, borderTopLeftRadius: radius }
            : {}

          const end = dayjs.utc(scheduleEntry.end)
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
    getActivityColor(scheduleEntry) {
      return scheduleEntry.activity().category().color
    },
  },
}
</script>
