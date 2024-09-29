<template>
  <Page :id="id" class="page">
    <ActivityListPeriod
      v-for="period in periods"
      :id="id"
      :period="period"
      :config="config"
      :content-type-names="['LearningObjectives', 'LearningTopics', 'Checklist']"
    />
  </Page>
</template>
<script>
import PdfComponent from '@/PdfComponent.js'
import ActivityListPeriod from './ActivityListPeriod.vue'

export default {
  name: 'ActivityList',
  components: { ActivityListPeriod },
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
