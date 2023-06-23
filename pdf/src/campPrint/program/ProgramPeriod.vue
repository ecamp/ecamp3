<template>
  <View :id="`${id}-${period.id}`" :bookmark="{ title: period.description, fit: true }" />
  <ScheduleEntry
    v-for="scheduleEntry in sortedScheduleEntries"
    :id="`${id}-${period.id}-${scheduleEntry.id}`"
    :key="scheduleEntry.id"
    :schedule-entry="scheduleEntry"
  />
</template>
<script>
import PdfComponent from '@/PdfComponent.js'
import ScheduleEntry from '../scheduleEntry/ScheduleEntry.vue'
import sortBy from 'lodash/sortBy'

export default {
  name: 'ProgramPeriod',
  components: { ScheduleEntry },
  extends: PdfComponent,
  props: {
    period: { type: Object, required: true },
  },
  computed: {
    sortedScheduleEntries() {
      return sortBy(this.period.scheduleEntries().items, [
        'dayNumber',
        'scheduleEntryNumber',
      ])
    },
  },
}
</script>
<pdf-style>
</pdf-style>
