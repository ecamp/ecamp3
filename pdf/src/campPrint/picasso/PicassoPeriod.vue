<template>
  <PicassoPage
    v-for="pageDays in pages"
    :id="id"
    :key="pageDays[0].id"
    :config="config"
    :content="content"
    :period="period"
    :days="pageDays"
    :bedtime="bedtimes.bedtime"
    :get-up-time="bedtimes.getUpTime"
    :time-step="timeStep"
  />
</template>
<script>
import PdfComponent from '@/PdfComponent.js'
import PicassoPage from './PicassoPage.vue'
import sortBy from 'lodash/sortBy.js'
import { splitDaysIntoPages, calculateBedtime } from '../../../common/helpers/picasso.js'

export default {
  name: 'PicassoPeriod',
  components: { PicassoPage },
  extends: PdfComponent,
  props: {
    config: { type: Object, required: true },
    content: { type: Object, required: true },
    period: { type: Object, required: true },
  },
  computed: {
    days() {
      return sortBy(this.period.days().items, (day) => this.$date.utc(day.start).unix())
    },
    pages() {
      const maxDaysPerPage = this.content.options.orientation === 'L' ? 8 : 4
      return splitDaysIntoPages(this.days, maxDaysPerPage)
    },
    timeStep() {
      // Height / duration of each picasso row, in hours
      return 1
    },
    bedtimes() {
      return calculateBedtime(
        this.period.scheduleEntries().items,
        this.$date,
        this.$date.utc(this.days[0].start),
        this.$date.utc(this.days[this.days.length - 1].end),
        this.timeStep
      )
    },
  },
}
</script>
<pdf-style>
</pdf-style>
