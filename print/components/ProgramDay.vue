<template>
  <div :class="showDailySummary && 'program-day-section'">
    <day-summary v-if="showDailySummary" :day="day" :schedule-entries="scheduleEntries" />

    <div v-if="showActivities">
      <schedule-entry
        v-for="scheduleEntry in scheduleEntries"
        :key="scheduleEntry.id"
        :schedule-entry="scheduleEntry"
        :index="index"
      />
    </div>
  </div>
</template>

<script>
import { filterMatchScheduleEntry } from '../common/helpers/filterMatchScheduleEntry.js'
import DaySummary from '~/components/DaySummary.vue'

export default {
  components: { DaySummary },
  props: {
    day: { type: Object, required: true },
    filter: { type: Object, default: () => ({}) },
    showDailySummary: { type: Boolean, required: true },
    showActivities: { type: Boolean, required: true },
    index: { type: Number, required: true },
  },
  computed: {
    // returns scheduleEntries of current day without the need for an additional API call
    scheduleEntries() {
      return this.day
        .period()
        .scheduleEntries()
        .items.filter((scheduleEntry) => {
          return (
            scheduleEntry.day()._meta.self === this.day._meta.self &&
            filterMatchScheduleEntry(scheduleEntry, this.filter)
          )
        })
    },
  },
}
</script>

<style scoped>
.program-day-section + .program-day-section {
  break-before: page;
}
</style>
