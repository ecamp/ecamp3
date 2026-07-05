<template>
  <template v-if="showDailySummary">
    <Page
      v-for="(
        {
          day,
          scheduleEntries: dayScheduleEntries,
          summaryScheduleEntries: daySummaryScheduleEntries,
        },
        dayIndex
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
        :summary-schedule-entries="daySummaryScheduleEntries"
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
      const scheduleEntries = this.period
        .scheduleEntries()
        .items.filter((scheduleEntry) => {
          return filterMatchScheduleEntry(scheduleEntry, { ...this.filter, day: [] })
        })

      if (!this.hasDayFilter) return scheduleEntries

      return scheduleEntries.filter((scheduleEntry) =>
        this.filteredDays.some(
          (day) => filterScheduleEntriesByDay([scheduleEntry], day, FULL_DAY_TIMES).length
        )
      )
    },
    overviewScheduleEntries() {
      return this.period.scheduleEntries().items.filter((scheduleEntry) => {
        return filterMatchScheduleEntry(scheduleEntry, { ...this.filter, day: [] })
      })
    },
    filteredDays() {
      return sortBy(this.period.days().items, (day) =>
        this.$date.utc(day.start).valueOf()
      ).filter((day) => this.dayMatchesFilter(day))
    },
    hasDayFilter() {
      return (
        this.filter.day !== null &&
        this.filter.day !== undefined &&
        this.filter.day.length > 0
      )
    },
    days() {
      return this.filteredDays
        .map((day) => ({
          day,
          scheduleEntries: this.scheduleEntriesForDay(day),
          summaryScheduleEntries: filterScheduleEntriesByDay(
            this.overviewScheduleEntries,
            day,
            FULL_DAY_TIMES
          ),
        }))
        .filter(({ summaryScheduleEntries }) => summaryScheduleEntries.length)
    },
  },
  methods: {
    dayMatchesFilter(day) {
      return (
        this.filter.day === null ||
        this.filter.day === undefined ||
        this.filter.day.length === 0 ||
        this.filter.day.includes(day._meta.self)
      )
    },
    scheduleEntriesForDay(day) {
      const previousDays = this.filteredDays.filter((otherDay) =>
        this.$date.utc(otherDay.start).isBefore(this.$date.utc(day.start))
      )

      return filterScheduleEntriesByDay(this.scheduleEntries, day, FULL_DAY_TIMES).filter(
        (scheduleEntry) =>
          !previousDays.some(
            (previousDay) =>
              filterScheduleEntriesByDay([scheduleEntry], previousDay, FULL_DAY_TIMES)
                .length
          )
      )
    },
  },
}
</script>
<style lang="react-pdf">
.program-period-title {
  font-size: 10pt;
  font-weight: bold;
  text-align: center;
}
</style>
