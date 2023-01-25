<template>
  <div style="position: relative">
    <v-btn class="float-right" text :href="url" target="_blank">
      <v-icon left>mdi-open-in-new</v-icon>
      {{ $tc('components.print.printNuxt.printPreviewNuxt.openPreview') }}
    </v-btn>

    <iframe
      id="previewIFrame"
      ref="previewIFrame"
      name="previewIFrame"
      :title="$tc('components.print.printNuxt.printPreviewNuxt.previewIframeTitle')"
      class="mt-3 d-block"
      frameborder="1"
      width="100%"
      height="1150"
      :src="url"
      v-bind="$attrs"
    />

    <v-overlay absolute :value="loading || error" z-index="2">
      <div v-if="error">
        {{ $tc('components.print.printNuxt.printPreviewNuxt.previewError') }}
      </div>
      <v-progress-circular v-else indeterminate />
    </v-overlay>
  </div>
</template>

<script>
const PRINT_URL = window.environment.PRINT_URL

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
