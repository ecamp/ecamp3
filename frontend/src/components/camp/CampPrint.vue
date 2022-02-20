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

      <v-btn
        color="primary"
        class="mt-5 ml-5"
        :loading="browserlessPrinting"
        @click="printBrowserless">
        Print via browserless.io
      </v-btn>

      <print-downloader
        v-for="result in results"
        :key="result.filename"
        :filename="result.filename"
        :title="result.title"
        class="mt-2" />
    </div>
    <local-print-preview :config="config"
                         width="100%"
                         height="500"
                         class="mt-4" />
  </div>
</template>

<script>
import PrintDownloader from '@/components/camp/CampPrintDownloader.vue'
import axios from 'axios'
import LocalPrintPreview from '../print/LocalPrintPreview.vue'

const PRINT_SERVER = window.environment?.PRINT_SERVER
const PRINT_FILE_SERVER = window.environment?.PRINT_FILE_SERVER
const BROWSERLESS_TOKEN = window.environment?.BROWSERLESS_TOKEN

export default {
  name: 'CampPrint',
  components: { PrintDownloader, LocalPrintPreview },
  props: {
    camp: {
      type: Function,
      required: true
    }
  },
  data () {
    return {
      printing: false,
      browserlessPrinting: false,
      results: [],
      config: {
        showFrontpage: true,
        showToc: true,
        showPicasso: true,
        showDailySummary: true,
        showStoryline: true,
        showActivities: true,
        camp: this.camp.bind(this)
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
                scheduleEntry.activity().activityResponsibles()._meta.loading ||
                scheduleEntry.activity().activityResponsibles().items.some(responsible => {
                  return responsible._meta.loading ||
                    responsible.campCollaboration()._meta.loading ||
                    (responsible.campCollaboration().user() !== null && responsible.campCollaboration().user()._meta.loading)
                }) ||
                scheduleEntry.activity().contentNodes()._meta.loading ||
                scheduleEntry.activity().contentNodes().items.some(contentNode => {
                  return contentNode._meta.loading ||
                    contentNode.contentType()._meta.loading
                })
            })
        }) ||
        this.camp().materialLists()._meta.loading ||
        this.camp().materialLists().items.some(materialList => {
          return materialList._meta.loading
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
    forceFileDownload (response, title) {
      const url = window.URL.createObjectURL(new Blob([response.data]))
      const link = document.createElement('a')
      link.href = url
      link.setAttribute('download', title)
      document.body.appendChild(link)
      link.click()
    },
    printBrowserless () {
      const title = 'PrintedByBrowserless.pdf'

      this.browserlessPrinting = true

      axios({
        method: 'post',
        url: `https://chrome.browserless.io/pdf?token=${BROWSERLESS_TOKEN}`,
        responseType: 'arraybuffer',
        withCredentials: false,
        headers: {
          'Cache-Control': 'no-cache',
          Pragma: 'no-cache',
          Expires: '0',
          'Content-Type': 'application/json'
        },
        data: {
          url: 'https://ecamp3-print-demo-gkuce.ondigitalocean.app?pagedjs=true',
          gotoOptions: {
            waitUntil: 'networkidle0'
          },
          options: {
            displayHeaderFooter: false,
            printBackground: true,
            format: 'A4',
            margin: {
              bottom: '0px',
              left: '0px',
              right: '0px',
              top: '0px'
            }
          }
        }
      })
        .then((response) => {
          this.browserlessPrinting = false
          this.forceFileDownload(response, title)
        })
        .catch((error) => {
          console.log(error)
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
