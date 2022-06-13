// You still need to register Vuetify itself
// src/plugins/vuetify.js

import Vuetify from 'vuetify/lib'
import PbsLogo from '@/assets/PbsLogo.svg'
import GoogleLogo from '@/assets/GoogleLogo.svg'
import eCampLogo from '@/assets/eCampLogo.svg'
import CeviLogo from '@/assets/CeviLogo.svg'
import i18n from '@/plugins/i18n'
import colors from 'vuetify/lib/util/colors'

class VuetifyLoaderPlugin {
  install(Vue) {
    Vue.use(Vuetify)

    const opts = {
      lang: {
        t: (key, ...params) => i18n.t(key, params),
      },
      icons: {
        values: {
          pbs: { component: PbsLogo },
          google: { component: GoogleLogo },
          ecamp: { component: eCampLogo },
          cevi: { component: CeviLogo },
        },
      },
      theme: {
        themes: {
          light: {
            error: colors.red.darken2,
          },
        },
      },
    }

    vuetify = new Vuetify(opts)
  }
}

export let vuetify

export default new VuetifyLoaderPlugin()
