<template>
  <div>
    <v-skeleton-loader v-if="camp()._meta.loading" type="article" />
    <div v-else>
      <h3>{{ $tc('components.camp.campPrint.selectPrintPreview') }}</h3>
      <e-checkbox v-model="config.showFrontpage" :name="$tc('components.camp.campPrint.frontpage')" />
      <e-checkbox v-model="config.showToc" :name="$tc('components.camp.campPrint.toc')" />
      <e-checkbox v-model="config.showPicasso" :name="$tc('components.camp.campPrint.picasso')" />
      <e-checkbox v-model="config.showStoryline" :name="$tc('components.camp.campPrint.storyline')" />
      <e-checkbox v-model="config.showDailySummary" :name="$tc('components.camp.campPrint.dailySummary')" />
      <e-checkbox v-model="config.showActivities" :name="$tc('components.camp.campPrint.activities')" />

      <v-btn color="primary" class="mt-5"
             :href="previewUrl"
             target="_blank">
        {{ $tc('components.camp.campPrint.openPrintPreview') }}
      </v-btn>
      <v-btn
        color="primary"
        class="mt-5 ml-5"
        :loading="printing"
        @click="print">
        {{ $tc('components.camp.campPrint.printNow') }}
      </v-btn>
      <print-downloader
        v-for="result in results"
        :key="result.filename"
        :filename="result.filename"
        :title="result.title"
        class="mt-2" />
    </div>
    <v-btn @click="refreshPreview">Refresh</v-btn>
    <print-react-pdf v-if="!dataLoading && !refreshing"
                     ref="printPreview"
                     :tc="boundTc"
                     :camp="camp()" />
  </div>
</template>

<script>
import PrintDownloader from '@/components/camp/CampPrintDownloader.vue'
import PrintReactPdf from '@/components/print/PrintReactPdf.js'

const PRINT_SERVER = window.environment.PRINT_SERVER
const PRINT_FILE_SERVER = window.environment.PRINT_FILE_SERVER

export default {
  name: 'CampPrint',
  components: { PrintDownloader, PrintReactPdf },
  props: {
    camp: {
      type: Function,
      required: true
    }
  },
  data () {
    return {
      printing: false,
      results: [],
      config: {
        showFrontpage: true,
        showToc: true,
        showPicasso: true,
        showDailySummary: true,
        showStoryline: true,
        showActivities: true
      },
      refreshing: false
    }
  },
  computed: {
    previewUrl () {
      const configGetParams = Object.entries(this.config).map(([key, val]) => `${key}=${val}`).join('&')
      return `${PRINT_SERVER}/?camp=${this.camp().id}&pagedjs=true&${configGetParams}&lang=${this.lang}`
    },
    lang () {
      return this.$store.state.lang.language
    },
    dataLoading () {
      return this.camp()._meta.loading ||
        this.camp().periods()._meta.loading ||
        this.camp().periods().items.some(period => {
          return period._meta.loading ||
            period.scheduleEntries()._meta.loading ||
            period.scheduleEntries().items.some(scheduleEntry => {
              return scheduleEntry._meta.loading ||
                scheduleEntry.activity()._meta.loading ||
                scheduleEntry.activity().category()._meta.loading ||
                scheduleEntry.activity().campCollaborations()._meta.loading ||
                scheduleEntry.activity().campCollaborations().items.some(responsible => {
                  return responsible._meta.loading ||
                    (responsible.user() !== null && responsible.user()._meta.loading)
                })
            })
        })
    },
    boundTc () {
      return this.$tc.bind(this)
    }
  },
  methods: {
    async print () {
      this.printing = true
      const result = await this.api.post('/printer', {
        campId: this.camp().id,
        config: { ...this.config, lang: this.lang }
      })
      this.printing = false
      this.results.push({
        filename: `${PRINT_FILE_SERVER}/${result.filename}-weasy.pdf`,
        title: 'ecamp3-weasy.pdf'
      })
      this.results.push({
        filename: `${PRINT_FILE_SERVER}/${result.filename}-puppeteer.pdf`,
        title: 'ecamp3-puppeteer.pdf'
      })
    },
    refreshPreview () {
      this.refreshing = true
      this.$nextTick(() => (this.refreshing = false))
    }
  }
}
</script>

<style scoped>
</style>
