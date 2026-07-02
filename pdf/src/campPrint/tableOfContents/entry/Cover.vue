<template>
  <Link class="toc-entry" :href="`#${id}`">
    <Text>{{ $tc('print.cover.title') }}</Text>
    <TocEntryPageNumber v-if="config.options.pageNumbers" :id="id" />
  </Link>
</template>
<script>
import PdfComponent from '@/PdfComponent.js'
import TocEntryPageNumber from '../TocEntryPageNumber.vue'

export default {
  name: 'Cover',
  components: { TocEntryPageNumber },
  extends: PdfComponent,
  props: {
    config: { type: Object, required: true },
  },
}
</script>
<style lang="react-pdf"></style>
