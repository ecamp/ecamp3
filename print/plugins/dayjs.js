import dayjs from '@/../common/helpers/dayjs.js'

export default defineNuxtPlugin((nuxtApp) => {
  // now available on `nuxtApp.$injected`
  nuxtApp.provide('date', dayjs)
})
