<template>
  <PicassoPeriod
    v-for="period in periods"
    :id="id"
    :key="period.id"
    :config="config"
    :content="content"
    :period="period"
  />
</template>
<script>
import PdfComponent from '@/PdfComponent.js'
import PicassoPeriod from './PicassoPeriod.vue'

export default {
  name: 'Picasso',
  components: { PicassoPeriod },
  extends: PdfComponent,
  props: {
    config: { type: Object, required: true },
    content: { type: Object, required: true },
  },
  computed: {
    periods() {
      return (this.content.options?.periods || []).map((periodUri) => {
        return this.api.get(periodUri)
      })
    },
  },
}
</script>
<pdf-style>
</pdf-style>
