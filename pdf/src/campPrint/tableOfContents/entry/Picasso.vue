<template>
  <Link v-for="period in periods" class="toc-entry" :href="`#${id}-${period.id}`">
    <Text>{{ $tc('print.picasso.title', { period: period.description }) }}</Text>
    <TocEntryPageNumber v-if="config.options.pageNumbers" :id="`${id}-${period.id}`" />
  </Link>
</template>
<script>
import PdfComponent from '@/PdfComponent.js'
import TocEntryPageNumber from '../TocEntryPageNumber.vue'

export default {
  name: 'Picasso',
  components: { TocEntryPageNumber },
  extends: PdfComponent,
  props: {
    entry: { type: Object, required: true },
    config: { type: Object, required: true },
  },
  computed: {
    periods() {
      return this.entry.options.periods.map((periodUri) => this.api.get(periodUri))
    },
  },
}
</script>
<style lang="react-pdf"></style>
