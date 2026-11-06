import dayjs from '@/helpers/dayjs.js'

export default {
  install: (app) => {
    Object.defineProperties(app.config.globalProperties, {
      $date: {
        get() {
          return dayjs
        },
      },
    })
  },
}
