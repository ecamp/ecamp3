<template>
  <div style="position: relative">
    <iframe
      :src="urlWithPagemodes"
      :title="$t('components.print.generator.printPreview.previewIframeTitle')"
      class="d-block"
      v-bind="$attrs"
      height="800px"
    />
    <v-overlay
      :model-value="loading || error"
      contained
      z-index="2"
      content-class="w-100 h-100 text-white"
    >
      <div v-if="error">
        {{ $t('components.print.generator.printPreview.previewError') }}
      </div>
      <div v-else class="d-flex flex-column gap-3 align-center h-100 justify-center">
        <v-progress-circular :model-value="progress" size="24"></v-progress-circular>
        <div>{{ state }}</div>
      </div>
    </v-overlay>
  </div>
</template>

<script>
import { generatePdf } from './generatePdf.js'
import { generatePdfMixin } from './generatePdfMixin.js'
import { componentI18n } from '@/plugins/index.js'
import { useToast } from '@/components/toast/useToast.js'
import { markRaw } from 'vue'

const RENDER_IN_WORKER = true

export default {
  name: 'PrintPreview',
  mixins: [generatePdfMixin],
  props: {
    config: {
      type: Object,
      default: () => ({}),
    },
  },
  setup() {
    const toast = useToast()
    return { toast }
  },
  data() {
    return {
      url: null,
      controller: null,
      renderTimer: null,
      error: null,
    }
  },
  computed: {
    urlWithPagemodes() {
      if (!this.url) return null
      return this.url + '#navpanes=0&pagemode=none&view=fit'
    },
  },
  watch: {
    config: {
      immediate: true,
      deep: true,
      handler() {
        this.scheduleRender()
      },
    },
  },
  unmounted() {
    clearTimeout(this.renderTimer)
    this.controller?.abort()
    this.revokeOldObjectUrl()
  },
  methods: {
    scheduleRender() {
      clearTimeout(this.renderTimer)
      this.controller?.abort()
      this.loading = true
      this.error = null
      this.renderTimer = setTimeout(() => this.generatePdf(), 250)
    },
    async generatePdf() {
      const controller = markRaw(new AbortController())
      this.controller = controller
      this.setProgress(0, 'loadingData')

      try {
        const { error, blob } = await generatePdf(
          {
            config: { ...this.config, apiGet: this.api.get.bind(this) },
            storeData: this.$store.state,
            translationData: componentI18n.messages.value,
            renderInWorker: RENDER_IN_WORKER,
          },
          (...args) => {
            if (!controller.signal.aborted) this.onProgress(...args)
          },
          controller.signal
        )
        if (controller.signal.aborted) return
        if (error) throw error
        this.revokeOldObjectUrl()
        this.url = URL.createObjectURL(blob)
        this.setProgress(100, 'done')
      } catch (error) {
        if (controller.signal.aborted) return
        this.error = error
        this.setProgress(100, 'failed')
      } finally {
        if (this.controller === controller && !controller.signal.aborted) {
          this.loading = false
          this.controller = null
        }
      }
    },
    revokeOldObjectUrl() {
      const oldUrl = this.url
      if (oldUrl) {
        this.url = null
        URL.revokeObjectURL(oldUrl)
      }
    },
  },
}
</script>

<style scoped></style>
