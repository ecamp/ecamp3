<template>
  <Text
    :id="`${id}-${period.id}`"
    :bookmark="{ title: period.description, fit: true }"
    class="program-period-title"
    >{{ $tc('print.program.title') }}: {{ period.description }}</Text
  >
  <ScheduleEntry
    v-for="scheduleEntry in sortedScheduleEntries"
    :id="`${id}-${period.id}-${scheduleEntry.id}`"
    :schedule-entry="scheduleEntry"
  />
</template>
<script>
import PdfComponent from '@/PdfComponent.js'
import ScheduleEntry from '../scheduleEntry/ScheduleEntry.vue'
import sortBy from 'lodash/sortBy.js'

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
.program-period-title {
  font-size: 10pt;
  font-weight: bold;
  text-align: center;
}
</pdf-style>
