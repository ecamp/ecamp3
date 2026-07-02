<template>
  <View class="toc-dotted-line" />
  <Text :render="() => $toc[id] ?? ''" class="toc-entry-page-number" />
</template>
<script>
import PdfComponent from '@/PdfComponent.js'

export default {
  name: 'TocEntryPageNumber',
  extends: PdfComponent,
  props: {
    id: { type: String, required: true },
  },
}
</script>
<style lang="react-pdf">
.toc-dotted-line {
  border-bottom: 1px dotted black;
  flex-grow: 1;
  margin-left: 6pt;
}
.toc-entry-page-number {
  width: 24pt;
  text-align: right;
}
</style>
