import { saveAs } from 'file-saver'
import slugify from 'slugify'
import { cloneDeep } from 'lodash'
import axios from 'axios'
import { getEnv } from '@/environment.js'

const PRINT_URL = getEnv().PRINT_URL

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
        locale: this.$store.state.lang.language.substring(0, 2),
      })

      this.loading = true

      try {
        const response = await axios({
          baseURL: null,
          method: 'get',
          url: `${PRINT_URL}/server/pdfChrome?config=${encodeURIComponent(
            JSON.stringify(config)
          )}`,
          responseType: 'arraybuffer',
          withCredentials: true,
          headers: {
            common: {
              'Cache-Control': 'no-cache',
              Pragma: 'no-cache',
              Expires: '0',
            },
          },
        })

        saveAs(new Blob([response.data]), config.documentName)
      } catch (error) {
        if (error?.response?.status === 503) {
          this.$toast.error(
            this.$tc('components.print.printNuxt.generatePdfMixin.queueFull')
          )
        } else {
          this.$toast.error(this.$tc('components.print.printNuxt.generatePdfMixin.error'))
        }
      } finally {
        this.loading = false
      }
    },
  },
}
