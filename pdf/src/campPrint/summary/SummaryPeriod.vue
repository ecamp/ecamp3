<template>
  <TocSectionStartMarker :id="`${id}-${period.id}`" />
  <Text
    :id="`${id}-${period.id}`"
    :bookmark="{ title: title + ': ' + period.description, fit: true }"
    class="summary-period-title"
    >{{ title }}: {{ period.description }}</Text
  >
  <SummaryDay
    v-for="day in days"
    :id="id"
    :period="period"
    :day="day"
    :content-type="contentType"
    :filter="filter"
  />
</template>
<script>
import PdfComponent from '@/PdfComponent.js'
import SummaryDay from './SummaryDay.vue'
import sortBy from 'lodash-es/sortBy.js'
import camelCase from 'lodash-es/camelCase.js'
import { filterMatchScheduleEntry } from '@/../common/helpers/filterMatchScheduleEntry.js'
import TocSectionStartMarker from '../TocSectionStartMarker.vue'

export default {
  name: 'SummaryPeriod',
  components: { TocSectionStartMarker, SummaryDay },
  extends: PdfComponent,
  props: {
    period: { type: Object, required: true },
    type: { type: String, required: true },
    contentType: { type: String, required: true },
    filter: { type: Object, default: () => ({}) },
  },
  computed: {
    days() {
      const days = this.period.days().items.filter((day) => {
        return (
          this.period
            .scheduleEntries()
            .items.filter(
              (scheduleEntry) =>
                scheduleEntry.day()._meta.self === day._meta.self &&
                filterMatchScheduleEntry(scheduleEntry, this.filter)
            ).length > 0
        )
      })
      return sortBy(days, (day) => this.$date.utc(day.start).unix())
    },
    title() {
      return this.$tc('print.' + this.camelCase(this.type) + '.title')
    },
  },
  methods: { camelCase },
}
</script>
<style lang="react-pdf">
.summary-period-title {
  font-size: 10pt;
  font-weight: bold;
  text-align: center;
}
</style>
