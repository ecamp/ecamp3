<template>
  <div>
    <v-btn color="primary"
           :loading="loading"
           outlined
           @click="generatePdf">
      <v-icon>mdi-printer</v-icon>
      <div class="mx-1">with</div>
      <v-icon>mdi-react</v-icon>
    </v-btn>
    <v-snackbar v-model="error" :timeout="10000">
      {{ $tc('components.print.localPdfDownloadButton.error') }}
      <template #action="{ attrs }">
        <v-btn color="red"
               text
               v-bind="attrs"
               @click="error = null">
          {{ $tc('global.button.close') }}
        </v-btn>
      </template>
    </v-snackbar>
  </div>
</template>

<script>
import { saveAs } from 'file-saver'
import slugify from 'slugify'

const RENDER_IN_WORKER = true

export default {
  name: 'PdfDownloadButtonReact',
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
  methods: {
    async generatePdf () {
      this.loading = true

      // lazy load generatePdf to avoid loading complete react-pdf when showing PDF download button
      const generatePdfModule = await import('./generatePdf.js')

      const { blob, error } = await generatePdfModule.generatePdf({
        config: { ...this.config, apiGet: this.api.get.bind(this) },
        storeData: this.$store.state,
        translationData: this.$i18n.messages,
        renderInWorker: RENDER_IN_WORKER
      })

      this.loading = false

      if (error) {
        this.error = error
        console.log(error)
        return
      }

      saveAs(blob, slugify(this.config.documentName, { locale: this.$store.state.lang.language.substr(0, 2) }))
    }
  }
}
</script>

<style scoped>
</style>
