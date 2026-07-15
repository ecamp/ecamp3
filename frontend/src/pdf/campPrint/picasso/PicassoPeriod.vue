<template>
  <PicassoPage
    v-for="pageDays in pages"
    :id="id"
    :config="config"
    :content="content"
    :period="period"
    :days="pageDays"
    :bedtime="bedtimes.bedtime"
    :get-up-time="bedtimes.getUpTime"
    :time-step="timeStep"
    :filter="filter"
  >
    <slot></slot>
  </PicassoPage>
</template>
<script>
import PdfComponent from '@/pdf/PdfComponent.js'
import PicassoPage from './PicassoPage.vue'
import sortBy from 'lodash-es/sortBy.js'
import { splitDaysIntoPages, calculateBedtime } from '@/common/helpers/picasso.js'
import { filterMatchScheduleEntry } from '@/common/helpers/filterMatchScheduleEntry.js'

export default {
  name: 'PicassoPeriod',
  components: { PicassoPage },
  extends: PdfComponent,
  props: {
    config: { type: Object, required: true },
    content: { type: Object, required: true },
    period: { type: Object, required: true },
    filter: { type: Object, default: () => ({}) },
  },
  computed: {
    days() {
      const dayFilter = this.content.options.filter?.day
      return sortBy(this.period.days().items, (day) =>
        this.$date.utc(day.start).unix()
      ).filter((day) => {
        if (!dayFilter || dayFilter.length === 0) return true
        return dayFilter.includes(day._meta.self)
      })
    },
    scheduleEntries() {
      return this.period.scheduleEntries().items.filter((scheduleEntry) => {
        return filterMatchScheduleEntry(scheduleEntry, this.content.options.filter)
      })
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
        this.scheduleEntries,
        this.$date,
        this.$date.utc(this.days[0].start),
        this.$date.utc(this.days[this.days.length - 1].end),
        this.timeStep
      )
    },
  },
}
</script>
<style lang="react-pdf"></style>
