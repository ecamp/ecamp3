import { saveAs } from 'file-saver'
import slugify from 'slugify'
import axios from 'axios'

const PRINT_SERVER = window.environment.PRINT_SERVER

export async function download (config) {
  return new Promise((resolve, reject) => {
    axios({
      method: 'get',
      url: `${PRINT_SERVER}/server/pdfChrome?config=${encodeURIComponent(JSON.stringify(config))}`,
      responseType: 'arraybuffer',
      withCredentials: true,
      headers: {
        'Cache-Control': 'no-cache',
        Pragma: 'no-cache',
        Expires: '0'
      }
    }).then(response => {
      saveAs(new Blob([response.data]), slugify(this.config.documentName, { locale: this.$store.state.lang.language.substr(0, 2) }))
      resolve()
    }).catch(error =>
      reject(error)
    )
  })
}
