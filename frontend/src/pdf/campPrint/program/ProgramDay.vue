<template>
  <DaySummary :day="day" :schedule-entries="summaryScheduleEntries" />
  <ScheduleEntryContents
    v-for="scheduleEntry in scheduleEntries"
    :id="`${id}-${period.id}-${scheduleEntry.id}`"
    :schedule-entry="scheduleEntry"
  />
</template>
<script>
import PdfComponent from '@/pdf/PdfComponent.js'
import DaySummary from './DaySummary.vue'
import ScheduleEntryContents from '../scheduleEntry/ScheduleEntryContents.vue'

export default {
  name: 'ProgramDay',
  components: { DaySummary, ScheduleEntryContents },
  extends: PdfComponent,
  props: {
    period: { type: Object, required: true },
    day: { type: Object, required: true },
    scheduleEntries: { type: Array, required: true },
    summaryScheduleEntries: { type: Array, required: true },
  },
}
</script>
<style lang="react-pdf"></style>
