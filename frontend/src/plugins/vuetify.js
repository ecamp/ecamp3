// You still need to register Vuetify itself
// src/plugins/vuetify.js

import { createVuetify } from 'vuetify'
import PbsLogo from '@/assets/PbsLogo.svg'
import GoogleLogo from '@/assets/GoogleLogo.svg'
import eCampLogo from '@/assets/eCampLogo.svg'
import CeviLogo from '@/assets/CeviLogo.svg'
import JublaLogo from '@/assets/JublaLogo.svg'
import TentDay from '@/assets/tents/TentDay.svg'
import PaperSize from '@/assets/icons/PaperSize.svg'
import BigScreen from '@/assets/icons/BigScreen.svg'
import ResponsiveLayout from '@/assets/icons/ResponsiveLayout.svg'
import ColumnLayout from '@/assets/icons/ColumnLayout.svg'
import i18n from '@/plugins/i18n'
import * as colors from 'vuetify/util/colors'
import { apiPropsMixin } from '@/mixins/apiPropsMixin'

// Styles
import 'vuetify/styles'

class VuetifyLoaderPlugin {
  install(app) {
    const opts = {
      lang: {
        t: (key, ...params) => i18n.tc(key, 0, params),
      },
      icons: {
        aliases: {
          pbs: 'mdi-close-circle',
          google: 'mdi-close-circle',
          ecamp: 'mdi-close-circle',
          cevi: 'mdi-close-circle',
          jubla: 'mdi-close-circle',
          tentDay: 'mdi-close-circle',
          paperSize: 'mdi-close-circle',
          bigScreen: 'mdi-close-circle',
          columnLayout: 'mdi-close-circle',
          responsiveLayout: 'mdi-close-circle',
        },
        sets: {
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

    vuetify = createVuetify(opts)

    app.use(vuetify)
  }
}

export let vuetify

export default new VuetifyLoaderPlugin()
