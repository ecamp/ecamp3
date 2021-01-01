// You still need to register Vuetify itself
// src/plugins/vuetify.js

import '@mdi/font/css/materialdesignicons.css'
import Vuetify from 'vuetify/lib'
import PbsLogo from '@/assets/PbsLogo.svg'
import GoogleLogo from '@/assets/GoogleLogo.svg'
import eCampLogo from '@/assets/eCampLogo.svg'
import i18n from '@/plugins/i18n'

class VuetifyLoaderPlugin {
  install (Vue, options) {
    Vue.use(Vuetify)

    const opts = {
      lang: {
        t: (key, ...params) => i18n.t(key, params)
      },
      icons: {
        values: {
          pbs: { component: PbsLogo },
          google: { component: GoogleLogo },
          ecamp: { component: eCampLogo }
        }
      }
    }

    vuetify = new Vuetify(opts)
  }
}

export let vuetify

export default new VuetifyLoaderPlugin()
