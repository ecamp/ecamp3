<template>
  <div>
    <v-btn class="ml-5"
           color="primary"
           :loading="loading"
           outlined
           @click="generatePdf">
      <v-icon>mdi-printer</v-icon>
      <div class="mx-1">with</div>
      <v-icon>mdi-react</v-icon>
    </v-btn>
    <v-snackbar v-model="error" :timeout="10000">
      {{ $tc('components.camp.print.localPdfDownloadButton.error') }}
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
import { generatePdf } from './generatePdf.js'
import { saveAs } from 'file-saver'
import slugify from 'slugify'

const RENDER_IN_WORKER = true

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
      loading: false,
      error: null
    }
  },
  methods: {
    async generatePdf () {
      this.loading = true

      const { blob, error } = await generatePdf({
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
