// You still need to register Vuetify itself
// src/plugins/vuetify.js

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
import * as colors from 'vuetify/util/colors'

import { VCalendar } from 'vuetify/labs/VCalendar'

// Styles
import '@mdi/font/css/materialdesignicons.css'
import 'vuetify/styles'
import { createVuetify } from 'vuetify'

class VuetifyLoaderPlugin {
  install(app) {
    const opts = {
      lang: {
        t: (key, ...params) => i18n.t(key, 0, params),
      },
      icons: {
        aliases: {
          pbs: PbsLogo,
          google: GoogleLogo,
          ecamp: eCampLogo,
          cevi: CeviLogo,
          jubla: JublaLogo,
          js: JSLogo,
          gs: GSLogo,
          tentDay: TentDay,
          paperSize: PaperSize,
          bigScreen: BigScreen,
          columnLayout: ColumnLayout,
          responsiveLayout: ResponsiveLayout,
        },
      },
      theme: {
        themes: {
          light: {
            error: colors.red.darken2,
          },
        },
      },
      components: {
        VCalendar,
      },
    }

    vuetify = createVuetify(opts)

    app.use(vuetify)
  }
}

export let vuetify

export default new VuetifyLoaderPlugin()
