<template>
  <div :class="showDailySummary && 'program-day-section'">
    <day-summary
      v-if="showDailySummary"
      :day="day"
      :schedule-entries="summaryScheduleEntries"
    />

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
import DaySummary from '~/components/DaySummary.vue'

export default {
  components: { DaySummary },
  props: {
    day: { type: Object, required: true },
    filter: { type: Object, default: () => ({}) },
    showDailySummary: { type: Boolean, required: true },
    showActivities: { type: Boolean, required: true },
    index: { type: Number, required: true },
    scheduleEntries: { type: Array, required: true },
    summaryScheduleEntries: { type: Array, default: () => [] },
  },
}
</script>

<style scoped>
.program-day-section + .program-day-section {
  break-before: page;
}
</style>
