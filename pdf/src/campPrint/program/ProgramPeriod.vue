<template>
  <TocSectionStartMarker :id="`${id}-${period.id}`" />
  <Text
    :id="`${id}-${period.id}`"
    :bookmark="{ title: period.description, fit: true }"
    class="program-period-title"
    >{{ $tc('print.program.title') }}: {{ period.description }}</Text
  >
  <template v-if="showDailySummary">
    <ProgramDay
      v-for="{ day, scheduleEntries: dayScheduleEntries } in days"
      :id="id"
      :period="period"
      :day="day"
      :schedule-entries="dayScheduleEntries"
    />
  </template>
  <template v-else>
    <ScheduleEntry
      v-for="scheduleEntry in scheduleEntries"
      :id="`${id}-${period.id}-${scheduleEntry.id}`"
      :schedule-entry="scheduleEntry"
    />
  </template>
</template>
<script>
import PdfComponent from '@/PdfComponent.js'
import ProgramDay from './ProgramDay.vue'
import ScheduleEntry from '../scheduleEntry/ScheduleEntry.vue'
import { filterMatchScheduleEntry } from '@/../common/helpers/filterMatchScheduleEntry.js'
import TocSectionStartMarker from '../TocSectionStartMarker.vue'
import sortBy from 'lodash-es/sortBy.js'

export default {
  name: 'ProgramPeriod',
  components: { TocSectionStartMarker, ProgramDay, ScheduleEntry },
  extends: PdfComponent,
  props: {
    period: { type: Object, required: true },
    filter: { type: Object, default: () => ({}) },
    showDailySummary: { type: Boolean, default: false },
  },
  computed: {
    scheduleEntries() {
      return this.period.scheduleEntries().items.filter((scheduleEntry) => {
        return filterMatchScheduleEntry(scheduleEntry, this.filter)
      })
    },
    days() {
      return sortBy(this.period.days().items, (day) =>
        this.$date.utc(day.start).valueOf()
      )
        .map((day) => ({
          day,
          scheduleEntries: this.scheduleEntries.filter(
            (scheduleEntry) => scheduleEntry.day()._meta.self === day._meta.self
          ),
        }))
        .filter(({ scheduleEntries }) => scheduleEntries.length)
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
