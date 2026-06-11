<template>
  <DaySummary :day="day" :schedule-entries="summaryScheduleEntries" />
  <ScheduleEntry
    v-for="scheduleEntry in scheduleEntries"
    :id="`${id}-${period.id}-${scheduleEntry.id}`"
    :schedule-entry="scheduleEntry"
  />
</template>
<script>
import PdfComponent from '@/PdfComponent.js'
import DaySummary from './DaySummary.vue'
import ScheduleEntry from '../scheduleEntry/ScheduleEntry.vue'

export default {
  name: 'ProgramDay',
  components: { DaySummary, ScheduleEntry },
  extends: PdfComponent,
  props: {
    period: { type: Object, required: true },
    day: { type: Object, required: true },
    scheduleEntries: { type: Array, required: true },
    summaryScheduleEntries: { type: Array, required: true },
  },
}
</script>
<pdf-style></pdf-style>
