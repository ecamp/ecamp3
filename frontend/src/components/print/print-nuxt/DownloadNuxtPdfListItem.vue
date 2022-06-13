<template>
  <v-list-item :disabled="loading" @click="generatePdf">
    <v-list-item-icon>
      <v-icon v-if="loading" class="mdi-spin">mdi-loading</v-icon>
      <v-icon v-else>mdi-nuxt</v-icon>
    </v-list-item-icon>
    <v-list-item-title>
      {{ $tc('components.print.printNuxt.downloadNuxtPdfListItem.label') }}
    </v-list-item-title>
  </v-list-item>
</template>

<script>
import { saveAs } from 'file-saver'
import slugify from 'slugify'

import axios from 'axios'

const PRINT_SERVER = window.environment.PRINT_SERVER

export default {
  name: 'DownloadNuxtPdfListItem',
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
  methods: {
    async generatePdf() {
      if (this.loading) {
        return
      }

      this.loading = true

      try {
        const response = await axios({
          method: 'get',
          url: `${PRINT_SERVER}/server/pdfChrome?config=${encodeURIComponent(
            JSON.stringify(this.config)
          )}`,
          responseType: 'arraybuffer',
          withCredentials: true,
          headers: {
            'Cache-Control': 'no-cache',
            Pragma: 'no-cache',
            Expires: '0',
          },
        })

        saveAs(
          new Blob([response.data]),
          slugify(this.config.documentName, {
            locale: this.$store.state.lang.language.substr(0, 2),
          })
        )
      } catch (error) {
        this.$emit('error', {
          label: this.$tc('components.print.printNuxt.downloadNuxtPdfListItem.error'),
          trace: error,
        })
      } finally {
        this.loading = false
      }
    },
  },
}
</script>

<style scoped></style>
