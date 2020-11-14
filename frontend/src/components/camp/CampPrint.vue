<template>
  <div>
    <v-skeleton-loader v-if="camp()._meta.loading" type="article" />
    <div v-else>
      <h3>Select print sections:</h3>
      <e-checkbox v-model="config.showFrontpage" :name="$tc('components.camp.CampPrint.frontpage')" />
      <e-checkbox v-model="config.showToc" :name="$tc('components.camp.CampPrint.toc')" />
      <e-checkbox v-model="config.showPicasso" :name="$tc('components.camp.CampPrint.picasso')" />
      <e-checkbox v-model="config.showStoryline" :name="$tc('components.camp.CampPrint.storyline')" />
      <e-checkbox v-model="config.showDailySummary" :name="$tc('components.camp.CampPrint.dailySummary')" />
      <e-checkbox v-model="config.showActivities" :name="$tc('components.camp.CampPrint.activities')" />

      <v-btn color="primary" class="mt-5"
             :href="previewUrl"
             target="_blank">
        {{ $tc('entity.camp.print.openPrintPreview') }}
      </v-btn>
      <v-btn
        color="primary"
        class="mt-5 ml-5"
        :loading="printing"
        @click="print">
        {{ $tc('entity.camp.print.printNow') }}
      </v-btn>
      <print-downloader
        v-for="result in results"
        :key="result.filename"
        :filename="result.filename"
        :title="result.title"
        class="mt-2" />
    </div>
  </div>
</template>

<script>
import PrintDownloader from '@/components/camp/CampPrintDownloader'

const PRINT_SERVER = window.environment.PRINT_SERVER
const PRINT_FILE_SERVER = window.environment.PRINT_FILE_SERVER

export default {
  name: 'CampPrint',
  components: { PrintDownloader },
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
      }
    }
  },
  computed: {
    previewUrl () {
      const configGetParams = Object.entries(this.config).map(([key, val]) => `${key}=${val}`).join('&')
      return `${PRINT_SERVER}/?camp=${this.camp().id}&pagedjs=true&${configGetParams}`
    }
  },
  methods: {
    async print () {
      this.printing = true
      const result = await this.api.post('/printer', {
        campId: this.camp().id,
        config: this.config
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
    }
  }
}
</script>

<style scoped>
</style>
