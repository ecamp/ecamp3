// You still need to register Vuetify itself
// src/plugins/vuetify.js

import PbsLogo from '@/assets/PbsLogo.svg'
import GoogleLogo from '@/assets/GoogleLogo.svg'
import eCampLogo from '@/assets/eCampLogo.svg'
import CeviLogo from '@/assets/CeviLogo.svg'
import JublaLogo from '@/assets/JublaLogo.svg'
import JSLogo from '@/assets/JSLogo.svg'
import GSLogo from '@/assets/GSLogo.svg'
import TentDay from '@/assets/tents/TentDay.svg'
import PaperSize from '@/assets/icons/PaperSize.svg'
import BigScreen from '@/assets/icons/BigScreen.svg'
import ResponsiveLayout from '@/assets/icons/ResponsiveLayout.svg'
import ColumnLayout from '@/assets/icons/ColumnLayout.svg'
import i18n from '@/plugins/i18n'
import { useI18n } from 'vue-i18n'
import DayJsAdapter from '@date-io/dayjs'
import en from 'dayjs/locale/en'

// Styles
import '@mdi/font/css/materialdesignicons.css'
import 'vuetify/styles'
import { createVuetify } from 'vuetify'
import { createVueI18nAdapter } from 'vuetify/locale/adapters/vue-i18n'

class VuetifyLoaderPlugin {
  install(app) {
    /**
     * @type VuetifyOptions
     */
    const opts = {
      locale: {
        adapter: createVueI18nAdapter({ i18n, useI18n }),
      },
      date: {
        adapter: DayJsAdapter,
        locale: { en },
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
            dark: false,
            colors: {
              background: '#FFFFFF',
              surface: '#FFFFFF',
              'surface-bright': '#FFFFFF',
              'surface-light': '#eceff1',
              'surface-variant': '#424242',
              'on-surface-variant': '#EEEEEE',
              skeleton: '#e0e0e0',
              primary: '#1867C0',
              'primary-darken-1': '#1F5592',
              secondary: '#424242',
              'secondary-darken-1': '#2f2f2f',
              error: '#d32f2f',
              info: '#2196F3',
              success: '#4CAF50',
              warning: '#FB8C00',
            },
            variables: {
              'border-color': '#000000',
              'border-opacity': 0.12,
              'high-emphasis-opacity': 0.87,
              'medium-emphasis-opacity': 0.6,
              'disabled-opacity': 0.38,
              'idle-opacity': 0.04,
              'hover-opacity': 0.04,
              'focus-opacity': 0.12,
              'selected-opacity': 0.08,
              'activated-opacity': 0.12,
              'pressed-opacity': 0.12,
              'dragged-opacity': 0.08,
              'theme-kbd': '#EEEEEE',
              'theme-on-kbd': '#000000',
              'theme-code': '#F5F5F5',
              'theme-on-code': '#000000',
            },
          },
          dark: {
            dark: true,
            colors: {
              background: '#121212',
              surface: '#212121',
              'surface-bright': '#ccbfd6',
              'surface-light': '#424242',
              'surface-variant': '#c8c8c8',
              'on-surface-variant': '#000000',
              skeleton: '#424242',
              primary: '#2196F3',
              'primary-darken-1': '#277CC1',
              secondary: '#424242',
              'secondary-darken-1': '#2f2f2f',
              error: '#CF6679',
              info: '#2196F3',
              success: '#4CAF50',
              warning: '#FB8C00',
            },
            variables: {
              'border-color': '#FFFFFF',
              'border-opacity': 0.12,
              'high-emphasis-opacity': 1,
              'medium-emphasis-opacity': 0.7,
              'disabled-opacity': 0.5,
              'idle-opacity': 0.1,
              'hover-opacity': 0.04,
              'focus-opacity': 0.12,
              'selected-opacity': 0.08,
              'activated-opacity': 0.12,
              'pressed-opacity': 0.16,
              'dragged-opacity': 0.08,
              'theme-kbd': '#424242',
              'theme-on-kbd': '#FFFFFF',
              'theme-code': '#343434',
              'theme-on-code': '#CCCCCC',
            },
          },
        },
      },
      defaults: {
        VBtn: {
          variant: 'elevated',
        },
        VField: {
          color: 'primary',
        },
      },
    }

    vuetify = createVuetify(opts)

    app.use(vuetify)
  }
}

export let vuetify

export default new VuetifyLoaderPlugin()
