<template>
  <Page :id="id" class="page">
    <StoryPeriod v-for="period in periods" :id="id" :key="period.id" :period="period" />
  </Page>
</template>
<script>
import PdfComponent from '@/PdfComponent.js'
import StoryPeriod from './StoryPeriod.vue'

export default {
  name: 'Story',
  components: { StoryPeriod },
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
