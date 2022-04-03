<template>
  <div style="position: relative">
    <pdf-download-button-nuxt :config="config" class="mb-3 float-left" />

    <v-btn
      class="ml-3 mb-3 float-left"
      color="primary"
      outlined
      :href="url"
      target="_blank">
      <v-icon left>mdi-open-in-new</v-icon>
      {{ $tc('components.print.printPreviewNuxt.openPreview') }}
    </v-btn>

    <iframe
      id="previewIFrame"
      ref="previewIFrame"
      name="previewIFrame"
      :title="$tc('views.camp.print.previewIframeTitle')"
      class="mt-3 d-block"
      frameborder="1"
      width="100%"
      height="1150"
      :src="url"
      v-bind="$attrs" />

    <v-overlay absolute :value="loading || error" z-index="2">
      <div v-if="error">
        {{ $tc('views.camp.print.previewError') }}
      </div>
      <v-progress-circular v-else indeterminate />
    </v-overlay>
  </div>
</template>

<script>
import PdfDownloadButtonNuxt from '@/components/print/print-nuxt/PdfDownloadButtonNuxt.vue'
const PRINT_SERVER = window.environment.PRINT_SERVER

export default {
  name: 'PrintPreviewNuxt',
  components: { PdfDownloadButtonNuxt },
  props: {
    config: {
      type: Object,
      default: () => {}
    }
  },
  data () {
    return {
      loading: false,
      error: null
    }
  },
  computed: {
    language () {
      return this.$store.state.lang.language
    },
    url () {
      return `${PRINT_SERVER}/?pagedjs=true&config=${encodeURIComponent(JSON.stringify(this.config))}`
    }
  }

}
</script>

<style scoped>
</style>
