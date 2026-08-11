import { saveAs } from 'file-saver'
import slugify from 'slugify'
import * as Sentry from '@sentry/browser'
import { generatePdf } from './generatePdf.js'
import { useToast } from 'vue-toastification'
import { componentI18n } from '@/plugins/index.js'

const RENDER_IN_WORKER = true

export const generatePdfMixin = {
  props: {
    config: {
      type: Object,
      default: () => {},
    },
  },
  setup() {
    const toast = useToast()
    return { toast }
  },
  data() {
    return {
      loading: false,
      progress: 0,
      state: '',
    }
  },
  computed: {
    progressBarGrowthFactor() {
      return 1 - Math.pow(0.01, 1 / this.estimatedNumberOfSteps)
    },
    estimatedNumberOfSteps() {
      // Rough estimate of the number of progress bar steps
      return (
        Math.max(this.estimatedNumberOfPages, 1) * 2 + this.config.contents.length + 5
      )
    },
    estimatedNumberOfPages() {
      return this.config.contents
        .map((content) => {
          const activityCount = content.options.filter?.activityCount || 10
          switch (content.type) {
            case 'Picasso':
              return 2
            case 'Story':
              return Math.ceil(0.1 * activityCount)
            case 'ActivityList':
              return Math.ceil(0.1 * activityCount)
            case 'SafetyConsiderations':
              return Math.ceil(0.1 * activityCount)
            case 'Program':
              return Math.ceil(0.5 * activityCount)
            default:
              return 1
          }
        })
        .reduce((sum, pages) => sum + pages, 0)
    },
  },
  methods: {
    async generatePdf() {
      if (this.loading) {
        return
      }

      this.loading = true
      this.setProgress(0, 'loadingData')

      const { blob, error } = await generatePdf(
        {
          config: { ...this.config, apiGet: this.api.get.bind(this) },
          storeData: this.$store.state,
          translationData: componentI18n.messages.value,
          renderInWorker: RENDER_IN_WORKER,
        },
        this.onProgress.bind(this)
      )

      if (error) {
        this.toast.error(this.$t('components.print.printClient.generatePdfMixin.error'))
        Sentry.captureException(new Error(error))
        this.setProgress(100, 'failed')
        this.loading = false
        return
      }

      this.onProgress('downloadingPdf')

      saveAs(
        blob,
        slugify(this.config.documentName, {
          locale: this.$store.state.lang.language.substring(0, 2),
        }) + '.pdf'
      )

      this.setProgress(100, 'done')
      this.loading = false
    },
    onProgress(state, params = {}) {
      const progress =
        100 * this.progressBarGrowthFactor +
        this.progress * (1 - this.progressBarGrowthFactor)
      this.setProgress(progress, state, params)
    },
    setProgress(progress, state = null, params = {}) {
      this.progress = progress
      this.state = this.$t(
        'components.print.printClient.generatePdfMixin.progress.' + state,
        1,
        params
      )
    },
  },
}
