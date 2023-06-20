<template>
  <Link
    v-for="period in periods"
    :key="period.id"
    class="toc-entry"
    :href="`#${id}-${period.id}`"
  >
    <Text>{{ $tc('print.picasso.title', { period: period.description }) }}</Text>
  </Link>
</template>
<script>
import PdfComponent from '@/PdfComponent.js'

export default {
  name: 'Picasso',
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
