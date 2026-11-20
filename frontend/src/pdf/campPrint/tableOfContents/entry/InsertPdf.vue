<template>
  <Link class="toc-entry" :href="`#${id}`">
    <Text>{{ entry.options.tocTitle?.trim() || $tc('print.insertPdf.title') }}</Text>
    <TocEntryPageNumber v-if="config.options.pageNumbers" :id="id" />
  </Link>
</template>
<script>
import PdfComponent from '@/pdf/PdfComponent.js'
import TocEntryPageNumber from '../TocEntryPageNumber.vue'

export default {
  name: 'InsertPdf',
  components: { TocEntryPageNumber },
  extends: PdfComponent,
  props: {
    entry: { type: Object, required: true },
    config: { type: Object, required: true },
  },
}
</script>
<style lang="react-pdf"></style>
