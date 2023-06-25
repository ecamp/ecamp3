<template>
  <Page :id="id" class="page program-page">
    <ProgramPeriod v-for="period in periods" :id="id" :period="period" />
  </Page>
</template>
<script>
import PdfComponent from '@/PdfComponent.js'
import ProgramPeriod from './ProgramPeriod.vue'

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
        .filter((period) => period.scheduleEntries().items.length)
    },
  },
}
</script>
<pdf-style>
.program-page {
  font-size: 8pt;
}
</pdf-style>
