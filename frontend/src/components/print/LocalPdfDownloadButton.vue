<template>
  <button class="ml-5 v-btn v-btn--outlined theme--light v-size--default primary--text"
          :disabled="loading"
          @click="exportPdf">
    {{ loading ? 'Generating...' : 'Generate PDF' }}
  </button>
</template>

<script>
import { generatePdf } from './generatePdf.js'
import { saveAs } from 'file-saver'

export default {
  name: 'LocalPDFDownloadButton',
  props: {
    config: {
      type: Object,
      default: () => {}
    }
  },
  data () {
    return {
      loading: false
    }
  },
  unmounted () {
    this.revokeOldObjectUrl()
  },
  methods: {
    async exportPdf () {
      this.loading = true

      const { blob, filename, error } = await generatePdf({
        config: this.config,
        storeData: this.$store.state,
        translationData: this.$i18n.messages,
        renderInWorker: false
      })

      if (error) {
        // TODO error handling
        console.log(error)
        this.loading = false
        return
      }

      this.loading = false
      saveAs(blob, filename)
    }
  }
}
</script>

<style scoped>
</style>
