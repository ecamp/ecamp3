<template>
  <div>
    <v-btn color="primary"
           :loading="loading"
           outlined
           @click="generatePdf">
      <v-icon>mdi-printer</v-icon>
      <div class="mx-1">with</div>
      <v-icon>mdi-nuxt</v-icon>
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

import axios from 'axios'

const PRINT_SERVER = window.environment.PRINT_SERVER

export default {
  name: 'PdfDownloadButtonNuxt',
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

      try {
        const response = await axios({
          method: 'get',
          url: `${PRINT_SERVER}/server/pdfChrome?config=${encodeURIComponent(JSON.stringify(this.config))}`,
          responseType: 'arraybuffer',
          withCredentials: true,
          headers: {
            'Cache-Control': 'no-cache',
            Pragma: 'no-cache',
            Expires: '0'
          }
        })

        saveAs(new Blob([response.data]), slugify(this.config.documentName, { locale: this.$store.state.lang.language.substr(0, 2) }))
      } catch (error) {
        this.error = error
        console.log(error)
      } finally {
        this.loading = false
      }
    }
  }
}
</script>

<style scoped>
</style>
