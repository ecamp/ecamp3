<template>
  <Text
    :id="`${id}-${period.id}`"
    :bookmark="{
      title:
        $tc('print.summary.title') +
        ' ' +
        translatedContentNode +
        (instanceNameFilter ? ` '${instanceNameFilter}'` : '') +
        ': ' +
        period.description,
      fit: true,
    }"
    class="summary-period-title"
    >{{ $tc('print.summary.title') }} {{ translatedContentNode
    }}<template v-if="instanceNameFilter"> "{{ instanceNameFilter }}"</template>:
    {{ period.description }}</Text
  >
  <SummaryDay
    v-for="day in days"
    :id="id"
    :period="period"
    :day="day"
    :content-type="contentType"
    :instance-name-filter="instanceNameFilter"
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
    instanceNameFilter: { type: String, default: '' },
  },
  computed: {
    days() {
      return sortBy(this.period.days().items, (day) => this.$date.utc(day.start).unix())
    },
    translatedContentNode() {
      return this.$tc(`contentNode.${camelCase(this.contentType)}.name`)
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
