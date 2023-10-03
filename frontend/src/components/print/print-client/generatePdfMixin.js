import { saveAs } from 'file-saver'
import slugify from 'slugify'
import * as Sentry from '@sentry/browser'
import { generatePdf } from './generatePdf.js'

const RENDER_IN_WORKER = true

export const generatePdfMixin = {
  props: {
    config: {
      type: Object,
      default: () => {},
    },
  },
  data() {
    return {
      loading: false,
    }
  },
  methods: {
    async generatePdf() {
      if (this.loading) {
        return
      }

      this.loading = true

      const { blob, error } = await generatePdf({
        config: { ...this.config, apiGet: this.api.get.bind(this) },
        storeData: this.$store.state,
        translationData: this.$i18n.messages,
        renderInWorker: RENDER_IN_WORKER,
      })

      if (error) {
        this.$toast.error(this.$tc('components.print.printClient.generatePdfMixin.error'))
        Sentry.captureException(new Error(error))
        this.loading = false
        return
      }

      saveAs(
        blob,
        slugify(this.config.documentName, {
          locale: this.$store.state.lang.language.substring(0, 2),
        }) + '.pdf'
      )

      this.loading = false
    },
  },
}
