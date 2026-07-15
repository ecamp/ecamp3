<template>
  <Link class="toc-entry" :href="`#${id}`">
    <Text>{{ $tc('print.activityList.title') }}</Text>
    <TocEntryPageNumber v-if="config.options.pageNumbers" :id="id" />
  </Link>
</template>
<script>
import PdfComponent from '@/pdf/PdfComponent.js'
import TocEntryPageNumber from '../TocEntryPageNumber.vue'

export default {
  name: 'ActivityList',
  components: { TocEntryPageNumber },
  extends: PdfComponent,
  props: {
    config: { type: Object, required: true },
  },
}
</script>
<style lang="react-pdf"></style>
