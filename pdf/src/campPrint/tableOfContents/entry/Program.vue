<template>
  <ProgramPeriod v-for="period in periods" :id="id" :period="period" />
</template>
<script>
import PdfComponent from '@/PdfComponent.js'
import ProgramPeriod from './ProgramPeriod.vue'

export default {
  name: 'Program',
  components: { ProgramPeriod },
  extends: PdfComponent,
  props: {
    entry: { type: Object, required: true },
  },
  computed: {
    periods() {
      return this.entry.options.periods.map((periodUri) => this.api.get(periodUri))
    },
  },
}
</script>
<pdf-style>
</pdf-style>
