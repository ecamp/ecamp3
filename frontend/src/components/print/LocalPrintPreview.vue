<template>
  <iframe :src="url"
          :title="$tc('views.camp.print.previewIframeTitle')"
          v-bind="$attrs" />
</template>

<script>
import { generatePdf } from './generatePdf.js'

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
      url: null
    }
  },
  watch: {
    config: {
      immediate: true,
      deep: true,
      async handler () {
        this.revokeOldObjectUrl()
        const { error, blob } = await generatePdf({
          config: this.config,
          storeData: this.$store.state,
          translationData: this.$i18n.messages,
          renderInWorker: false
        })
        if (error) {
          // TODO error handling
          console.log(error)
        } else {
          this.url = URL.createObjectURL(blob)
        }
      }
    }
  },
  unmounted () {
    this.revokeOldObjectUrl()
  },
  methods: {
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
