// You still need to register Vuetify itself
// src/plugins/vuetify.js

import Vuetify from 'vuetify/lib'
import PbsLogo from './icons/PbsLogo.vue'
import GoogleLogo from './icons/GoogleLogo.vue'
import eCampLogo from './icons/eCampLogo.vue'
import CeviLogo from './icons/CeviLogo.vue'
import JublaLogo from './icons/JublaLogo.vue'
import TentDay from './icons/TentDay.vue'
import PaperSize from './icons/PaperSize.vue'
import BigScreen from './icons/BigScreen.vue'
import ResponsiveLayout from './icons/ResponsiveLayout.vue'
import ColumnLayout from './icons/ColumnLayout.vue'
import i18n from '@/plugins/i18n'
import colors from 'vuetify/lib/util/colors'

class VuetifyLoaderPlugin {
  install(Vue) {
    Vue.use(Vuetify)

    const opts = {
      lang: {
        t: (key, ...params) => i18n.tc(key, 0, params),
      },
      icons: {
        values: {
          pbs: { component: PbsLogo },
          google: { component: GoogleLogo },
          ecamp: { component: eCampLogo },
          cevi: { component: CeviLogo },
          jubla: { component: JublaLogo },
          tentDay: { component: TentDay },
          paperSize: { component: PaperSize },
          bigScreen: { component: BigScreen },
          columnLayout: { component: ColumnLayout },
          responsiveLayout: { component: ResponsiveLayout },
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
