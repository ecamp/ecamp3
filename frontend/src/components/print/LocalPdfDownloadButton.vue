<template>
  <button class="ml-5 v-btn v-btn--outlined theme--light v-size--default primary--text"
          :disabled="loading"
          @click="generatePdf">
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
  methods: {
    async generatePdf () {
      this.loading = true

      const { blob, filename, error } = await generatePdf({
        config: this.config,
        storeData: this.$store.state,
        translationData: this.$i18n.messages,
        renderInWorker: true
      })

      this.loading = false

      if (error) {
        // TODO error handling
        console.log(error)
        return
      }

      saveAs(blob, filename)
    }
  }
}
</script>

<style scoped>
</style>
