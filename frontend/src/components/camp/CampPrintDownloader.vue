<template>
  <v-alert
    :type="printing ? 'info' : 'success'">
    <div v-if="printing">
      <v-progress-circular
        indeterminate
        size="24"
        color="primary" />
      Printing now...
    </div>
    <div v-else>
      Open PDF: <a :href="filename" target="_blank">{{ title }}</a>
    </div>
  </v-alert>
</template>

<script>
import axios from 'axios'

export default {
  name: 'CampPrintDownloader',
  components: { },
  props: {
    filename: {
      type: String,
      required: true
    },
    title: {
      type: String,
      required: true
    }
  },
  data () {
    return {
      printing: true
    }
  },
  mounted () {
    this.downloadWithAxios(this.filename, this.title)
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
    downloadWithAxios (url, title) {
      axios({
        method: 'get',
        url,
        responseType: 'arraybuffer',
        withCredentials: false
      })
        .then((response) => {
          this.printing = false
          this.forceFileDownload(response, title)
        })
        .catch((error) => {
          if (error.response.status === 404) {
            setTimeout(() => this.downloadWithAxios(url, title), 2000)
          }
        })
    }
  }
}
</script>

<style scoped>
</style>
