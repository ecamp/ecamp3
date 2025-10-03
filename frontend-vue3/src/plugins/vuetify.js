// You still need to register Vuetify itself
// src/plugins/vuetify.js

import PbsLogo from './icons/PbsLogo.vue'
import GoogleLogo from './icons/GoogleLogo.vue'
import eCampLogo from './icons/eCampLogo.vue'
import CeviLogo from './icons/CeviLogo.vue'
import JublaLogo from './icons/JublaLogo.vue'
import JSLogo from './icons/JSLogo.vue'
import GSLogo from './icons/GSLogo.vue'
import TentDay from './icons/TentDay.vue'
import PaperSize from './icons/PaperSize.vue'
import BigScreen from './icons/BigScreen.vue'
import ResponsiveLayout from './icons/ResponsiveLayout.vue'
import ColumnLayout from './icons/ColumnLayout.vue'
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
