<template>
  <div style="position: relative">
    <v-btn :href="url" class="float-right" target="_blank" variant="text">
      <v-icon start>mdi-open-in-new</v-icon>
      {{ $t('components.print.printNuxt.printPreviewNuxt.openPreview') }}
    </v-btn>

    <iframe
      id="previewIFrame"
      ref="previewIFrame"
      name="previewIFrame"
      :title="$t('components.print.printNuxt.printPreviewNuxt.previewIframeTitle')"
      class="mt-3 d-block"
      frameborder="1"
      width="100%"
      height="1150"
      :src="url"
      v-bind="$attrs"
    />

    <v-overlay :model-value="loading || error" absolute z-index="2">
      <div v-if="error">
        {{ $t('components.print.printNuxt.printPreviewNuxt.previewError') }}
      </div>
      <v-progress-circular v-else indeterminate />
    </v-overlay>
  </div>
</template>

<script>
import { getEnv } from '@/environment.js'

const PRINT_URL = getEnv().PRINT_URL

export default {
  name: 'PrintPreviewNuxt',
  props: {
    config: {
      type: Object,
      default: () => {},
    },
  },
  data() {
    return {
      loading: false,
      error: null,
    }
  },
  computed: {
    language() {
      return this.$store.state.lang.language
    },
    url() {
      return `${PRINT_URL}/?config=${encodeURIComponent(JSON.stringify(this.config))}`
    },
  },
}
</script>

<style scoped></style>
