<template>
  <template v-if="showDailySummary">
    <Page
      v-for="(
        { day, scheduleEntries: dayScheduleEntries, summaryScheduleEntries }, dayIndex
      ) in days"
      :id="isFirstPeriod && dayIndex === 0 ? id : undefined"
      class="page program-page"
      :size="config.options.pageSize || 'A4'"
    >
      <slot />
      <template v-if="dayIndex === 0">
        <TocSectionStartMarker :id="`${id}-${period.id}`" />
        <Text
          :id="`${id}-${period.id}`"
          :bookmark="{ title: period.description, fit: true }"
          class="program-period-title"
          >{{ $tc('print.program.title') }}: {{ period.description }}</Text
        >
      </template>
      <ProgramDay
        :id="id"
        :period="period"
        :day="day"
        :schedule-entries="dayScheduleEntries"
        :summary-schedule-entries="summaryScheduleEntries"
      />
    </Page>
  </template>
  <template v-else>
    <TocSectionStartMarker :id="`${id}-${period.id}`" />
    <Text
      :id="`${id}-${period.id}`"
      :bookmark="{ title: period.description, fit: true }"
      class="program-period-title"
      >{{ $tc('print.program.title') }}: {{ period.description }}</Text
    >
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
import { filterScheduleEntriesByDay } from '@/../common/helpers/picasso.js'
import TocSectionStartMarker from '../TocSectionStartMarker.vue'
import sortBy from 'lodash-es/sortBy.js'

const FULL_DAY_TIMES = [
  [0, 1],
  [24, 0],
]

export default {
  name: 'ProgramPeriod',
  components: { TocSectionStartMarker, ProgramDay, ScheduleEntry },
  extends: PdfComponent,
  props: {
    period: { type: Object, required: true },
    filter: { type: Object, default: () => ({}) },
    showDailySummary: { type: Boolean, default: false },
    config: { type: Object, required: true },
    isFirstPeriod: { type: Boolean, default: false },
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
          summaryScheduleEntries: filterScheduleEntriesByDay(
            this.scheduleEntries,
            day,
            FULL_DAY_TIMES
          ),
        }))
        .filter(({ summaryScheduleEntries }) => summaryScheduleEntries.length)
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
