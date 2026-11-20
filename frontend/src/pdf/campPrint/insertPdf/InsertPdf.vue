<template>
  <Page
    v-for="(page, index) in pages"
    :id="index === 0 ? id : undefined"
    :key="index"
    :size="pageSize(page)"
    class="page"
  >
    <slot></slot>
    <TocSectionStartMarker v-if="index === 0" :id="id" />
    <View
      :render="
        ({ pageNumber }) => {
          $insertedPages[pageNumber] = {
            assetId: content.options.file.assetId,
            pageIndex: page.index,
          }
        }
      "
    />
  </Page>
</template>
<script>
import PdfComponent from '@/pdf/PdfComponent.js'
import TocSectionStartMarker from '../TocSectionStartMarker.vue'
import { parsePageRange } from './pageRange.js'

export default {
  name: 'InsertPdf',
  components: { TocSectionStartMarker },
  extends: PdfComponent,
  props: {
    content: { type: Object, required: true },
    config: { type: Object, required: true },
  },
  computed: {
    pages() {
      const pages = this.content.options.file?.pages ?? []
      return parsePageRange(this.content.options.pageRange, pages.length).map(
        (index) => ({ index, ...pages[index] })
      )
    },
  },
  methods: {
    pageSize(page) {
      return this.content.options.fitToPageSize
        ? this.config.options.pageSize || 'A4'
        : [page.width, page.height]
    },
  },
}
</script>
<style lang="react-pdf"></style>
