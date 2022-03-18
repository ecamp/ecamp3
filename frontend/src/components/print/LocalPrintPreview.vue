<template>
  <div style="position: relative">
    <iframe :src="url"
            :title="$tc('views.camp.print.previewIframeTitle')"
            class="d-block"
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
import { generatePdf } from './generatePdf.js'

const RENDER_IN_WORKER = true

export default {
  name: 'LocalPrintPreview',
  props: {
    config: {
      type: Object,
      default: () => {}
    }
  },
  data () {
    return {
      url: null,
      loading: false,
      preventingMultiple: false,
      error: null
    }
  },
  computed: {
    language () {
      return this.$store.state.lang.language
    }
  },
  watch: {
    config: {
      immediate: true,
      deep: true,
      handler () { this.generatePdf() }
    },
    language: {
      immediate: true,
      handler () { this.generatePdf() }
    }
  },
  unmounted () {
    this.revokeOldObjectUrl()
  },
  methods: {
    async generatePdf () {
      if (this.loading) {
        this.preventingMultiple = true
        return
      }

      this.loading = true
      this.error = null
      this.revokeOldObjectUrl()

      const { error, blob } = await generatePdf({
        config: { ...this.config, apiGet: this.api.get.bind(this) },
        storeData: this.$store.state,
        translationData: this.$i18n.messages,
        renderInWorker: RENDER_IN_WORKER
      })

      if (error) {
        this.error = error
        console.log(error)
      } else {
        this.url = URL.createObjectURL(blob)
      }

      this.loading = false
      if (this.preventingMultiple) {
        this.preventingMultiple = false
        this.generatePdf()
      }
    },
    revokeOldObjectUrl () {
      const oldUrl = this.url
      if (oldUrl) {
        this.url = null
        URL.revokeObjectURL(oldUrl)
      }
    }
  }
}
</script>

<style scoped>
</style>
