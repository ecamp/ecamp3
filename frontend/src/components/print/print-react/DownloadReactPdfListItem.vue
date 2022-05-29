<template>
  <v-list-item @click="generatePdf">
    <v-list-item-icon>
      <v-icon v-if="loading" class="mdi-spin">mdi-loading</v-icon>
      <v-icon v-else>mdi-react</v-icon>
    </v-list-item-icon>
    <v-list-item-title>
      {{ $tc("components.print.localPdfDownloadButton.label") }}
    </v-list-item-title>
  </v-list-item>
</template>

<script>
import { saveAs } from 'file-saver'
import slugify from 'slugify'

const RENDER_IN_WORKER = true

export default {
  name: 'DownloadReactPdfListItem',
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
      if (this.loading) {
        return
      }

      this.loading = true

      // lazy load generatePdf to avoid loading complete react-pdf when showing PDF download buton
      const generatePdfModule = await import('./generatePdf.js')

      const { blob, error } = await generatePdfModule.generatePdf({
        config: { ...this.config, apiGet: this.api.get.bind(this) },
        storeData: this.$store.state,
        translationData: this.$i18n.messages,
        renderInWorker: RENDER_IN_WORKER
      })

      if (error) {
        this.$emit('error', {
          label: this.$tc('components.print.localPdfDownloadButton.error'),
          trace: error
        })
        return
      }

      saveAs(
        blob,
        slugify(this.config.documentName, {
          locale: this.$store.state.lang.language.substr(0, 2)
        })
      )

      this.loading = false
    }
  }
}
</script>

<style scoped></style>
