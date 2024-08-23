<template>
  <Page :id="id" class="page">
    <SummaryPeriod
      v-for="period in periods"
      :id="id"
      :period="period"
      :content-type="content.options.contentType"
      :instance-name-filter="content.options.instanceNameFilter ?? ''"
    />
  </Page>
</template>
<script>
import PdfComponent from '@/PdfComponent.js'
import SummaryPeriod from './SummaryPeriod.vue'

export default {
  name: 'Summary',
  components: { SummaryPeriod },
  extends: PdfComponent,
  props: {
    content: { type: Object, required: true },
    config: { type: Object, required: true },
  },
  computed: {
    periods() {
      return this.content.options.periods.map((periodUri) => this.api.get(periodUri))
    },
  },
}
</script>
<pdf-style>
</pdf-style>
