<template>
  <Link v-for="period in periods" :key="period.id" :href="`#${id}-${period.id}`">
    <Text>{{ $tc('print.story.title') }}: {{ period.description }}</Text>
  </Link>
</template>
<script>
import PdfComponent from '@/PdfComponent.js'

export default {
  name: 'Story',
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
