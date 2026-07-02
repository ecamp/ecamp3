<template>
  <template v-if="showDailySummary">
    <ProgramPeriod
      v-for="(period, periodIndex) in periods"
      :id="id"
      :period="period"
      :filter="content.options.filter"
      :config="config"
      :is-first-period="periodIndex === 0"
      show-daily-summary
    >
      <slot />
    </ProgramPeriod>
  </template>
  <Page v-else :id="id" class="page program-page" :size="config.options.pageSize || 'A4'">
    <slot />
    <ProgramPeriod
      v-for="period in periods"
      :id="id"
      :period="period"
      :filter="content.options.filter"
      :config="config"
    />
  </Page>
</template>
<script>
import PdfComponent from '@/PdfComponent.js'
import ProgramPeriod from './ProgramPeriod.vue'
import { filterMatchScheduleEntry } from '@/../common/helpers/filterMatchScheduleEntry.js'

export default {
  name: 'Program',
  components: { ProgramPeriod },
  extends: PdfComponent,
  props: {
    content: { type: Object, required: true },
    config: { type: Object, required: true },
  },
  computed: {
    showDailySummary() {
      return this.content.options.dayOverview || false
    },
    periods() {
      return this.content.options.periods
        .map((periodUri) => this.api.get(periodUri))
        .filter((period) => {
          return period
            .scheduleEntries()
            .items.filter((scheduleEntry) =>
              filterMatchScheduleEntry(scheduleEntry, this.content.options.filter)
            ).length
        })
    },
  },
}
</script>
<style lang="react-pdf">
.program-page {
  font-size: 8pt;
}
</style>
