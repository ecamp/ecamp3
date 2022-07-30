import dayjs from '@/common/helpers/dayjs.js'

export default {
  install: (Vue) => {
    Object.defineProperties(Vue.prototype, {
      $date: {
        get() {
          return dayjs
        },
      },
    })
    Vue.dayjs = dayjs
  },
}
