<template>
  <PicassoPeriod
    v-for="period in periods"
    :id="id"
    :config="config"
    :content="content"
    :period="period"
  >
    <slot></slot>
  </PicassoPeriod>
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
<style lang="react-pdf"></style>
