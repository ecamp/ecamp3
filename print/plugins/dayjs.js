import dayjs from '@/common/helpers/dayjs.js'

// avoid tree-shaking of dayjs/locales
import('dayjs/locale/en-gb')
import('dayjs/locale/de')
import('dayjs/locale/de-ch')
import('dayjs/locale/fr')
import('dayjs/locale/fr-ch')
import('dayjs/locale/it')
import('dayjs/locale/it-ch')

export default defineNuxtPlugin((nuxtApp) => {
  // now available on `nuxtApp.$injected`
  nuxtApp.provide('date', dayjs)
})
