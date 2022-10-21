import { saveAs } from 'file-saver'
import slugify from 'slugify'

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

      // lazy load generatePdf to avoid loading complete react-pdf when showing PDF download button
      const generatePdfModule = await import('./generatePdf.js')

      const { blob, error } = await generatePdfModule.generatePdf({
        config: { ...this.config, apiGet: this.api.get.bind(this) },
        storeData: this.$store.state,
        translationData: this.$i18n.messages,
        renderInWorker: RENDER_IN_WORKER,
      })

      if (error) {
        this.$toast.error(
          this.$tc('components.print.printReact.downloadReactPdfListItem.error')
        )
        this.loading = false
        return
      }

      saveAs(
        blob,
        slugify(this.config.documentName, {
          locale: this.$store.state.lang.language.substr(0, 2),
        })
      )

      this.loading = false
    },
  },
}
