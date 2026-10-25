<template>
  <Page
    :id="id"
    class="page summary-page"
    :class="{ 'page-compact': compact }"
    :size="config.options.pageSize || 'A4'"
  >
    <slot></slot>
    <SummaryPeriod
      v-for="period in periods"
      :id="id"
      :period="period"
      :type="content.type"
      :content-type="content.options.contentType"
      :filter="content.options.filter"
    />
  </Page>
</template>
<script>
import PdfComponent from '@/pdf/PdfComponent.js'
import SummaryPeriod from './SummaryPeriod.vue'

export default {
  name: 'SafetyConsiderations',
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
<style lang="react-pdf">
.summary-page {
  font-size: 10pt;
}
</style>
