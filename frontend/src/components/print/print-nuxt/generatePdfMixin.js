import { saveAs } from 'file-saver'
import slugify from 'slugify'
import { cloneDeep } from 'lodash'
import axios from 'axios'

const PRINT_URL = window.environment.PRINT_URL

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

      const config = cloneDeep(this.config)
      config.documentName = slugify(config.documentName, {
        locale: this.$store.state.lang.language.substr(0, 2),
      })

      this.loading = true

      try {
        const response = await axios({
          method: 'get',
          url: `${PRINT_URL}/server/pdfChrome?config=${encodeURIComponent(
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

        saveAs(new Blob([response.data]), config.documentName)
      } catch (error) {
        if (error?.response?.status === 503) {
          this.$toast.error(this.$tc('components.print.printNuxt.queueFull'))
        } else {
          this.$toast.error(this.$tc('components.print.printNuxt.error'))
        }
      } finally {
        this.loading = false
      }
    },
  },
}
