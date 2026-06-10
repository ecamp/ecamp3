<template>
  <Page :id="id" class="page program-page" :size="config.options.pageSize || 'A4'">
    <slot></slot>
    <ProgramPeriod
      v-for="period in periods"
      :id="id"
      :period="period"
      :filter="content.options.filter"
      :show-daily-summary="content.options.dayOverview || false"
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
<pdf-style>
.program-page {
  font-size: 8pt;
}
</pdf-style>
