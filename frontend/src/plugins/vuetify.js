// You still need to register Vuetify itself
// src/plugins/vuetify.js

import Vuetify from 'vuetify/lib'
import PbsLogo from '@/assets/PbsLogo.svg'
import GoogleLogo from '@/assets/GoogleLogo.svg'
import eCampLogo from '@/assets/eCampLogo.svg'
import CeviLogo from '@/assets/CeviLogo.svg'
import JublaLogo from '@/assets/JublaLogo.svg'
import JSLogo from '@/common/assets/logos/JSLogo.svg'
import GSLogo from '@/common/assets/logos/GSLogo.svg'
import TentDay from '@/assets/tents/TentDay.svg'
import PaperSize from '@/assets/icons/PaperSize.svg'
import BigScreen from '@/assets/icons/BigScreen.svg'
import ResponsiveLayout from '@/assets/icons/ResponsiveLayout.svg'
import ColumnLayout from '@/assets/icons/ColumnLayout.svg'
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
          js: { component: JSLogo },
          gs: { component: GSLogo },
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
