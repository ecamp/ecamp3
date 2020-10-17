<template>
  <div>
    <v-skeleton-loader v-if="camp()._meta.loading" type="article" />
    <div v-else>
      <v-btn color="primary" class="mt-5"
             :href="previewUrl"
             target="_blank">
        Open print preview
      </v-btn>
      <v-btn
        color="primary"
        class="mt-5 ml-5"
        :loading="printing"
        @click="print">
        Print now
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
      results: []
    }
  },
  computed: {
    previewUrl () {
      return `${PRINT_SERVER}/?camp=${this.camp().id}&pagedjs=true`
    }
  },
  methods: {
    async print () {
      this.printing = true
      const result = await this.api.post('/printer', {
        campId: this.camp().id
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
