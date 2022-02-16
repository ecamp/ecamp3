<template>
  <div>
    <v-btn class="ml-5"
           color="primary"
           :loading="loading"
           outlined
           @click="generatePdf">
      <v-icon>mdi-printer</v-icon>
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

      const { blob, filename, error } = await generatePdf({
        config: { ...this.config, apiGet: this.api.get.bind(this) },
        storeData: this.$store.state,
        translationData: this.$i18n.messages,
        renderInWorker: true
      })

      this.loading = false

      if (error) {
        this.error = error
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
