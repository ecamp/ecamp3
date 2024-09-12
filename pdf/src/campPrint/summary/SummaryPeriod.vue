<template>
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
  />
</template>
<script>
import PdfComponent from '@/PdfComponent.js'
import SummaryDay from './SummaryDay.vue'
import sortBy from 'lodash/sortBy.js'
import camelCase from 'lodash/camelCase.js'

export default {
  name: 'SummaryPeriod',
  components: { SummaryDay },
  extends: PdfComponent,
  props: {
    period: { type: Object, required: true },
    contentType: { type: String, required: true },
  },
  computed: {
    days() {
      return sortBy(this.period.days().items, (day) => this.$date.utc(day.start).unix())
    },
    title() {
      return this.$tc('print.summary.' + this.camelCase(this.contentType) + '.title')
    },
  },
  methods: { camelCase },
}
</script>
<pdf-style>
.summary-period-title {
  font-size: 10pt;
  font-weight: bold;
  text-align: center;
}
</pdf-style>
