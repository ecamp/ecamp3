<template>
  <v-container fluid>
    <content-card title="Print demo" max-width="800" toolbar>
      <v-card-text>
        <v-btn color="primary" class="mt-5"
               :href="previewUrl"
               target="_blank">
          {{ $tc('components.camp.campPrint.openPrintPreview') }}
        </v-btn>

        <v-btn
          color="primary"
          class="mt-5 ml-5"
          :loading="browserlessPrinting"
          @click="printBrowserless">
          Print via browserless.io
        </v-btn>
      </v-card-text>
    </content-card>
  </v-container>
</template>

<script>
import ContentCard from '@/components/layout/ContentCard.vue'
import axios from 'axios'

const BROWSERLESS_TOKEN = import.meta.env.VITE_BROWSERLESS_TOKEN
const PRINT_SERVER = import.meta.env.VITE_PRINT_SERVER

export default {
  name: 'Print',
  components: { ContentCard },
  data () {
    return {
      browserlessPrinting: false
    }
  },
  computed: {
    previewUrl () {
      return `${PRINT_SERVER}/?pagedjs=true`
    }
  },
  methods: {
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
          url: this.previewUrl,
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
    }
  }
}
</script>

<style lang="scss" scoped>
</style>
