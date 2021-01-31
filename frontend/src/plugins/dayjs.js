import Vue from 'vue'
import dayjs from 'dayjs'

import 'dayjs/locale/de'
import 'dayjs/locale/de-ch'
import 'dayjs/locale/fr'

import utc from 'dayjs/plugin/utc'
import customParseFormat from 'dayjs/plugin/customParseFormat'
import localizedFormat from 'dayjs/plugin/localizedFormat'

dayjs.extend(utc)
dayjs.extend(customParseFormat)
dayjs.extend(localizedFormat)

const formatStrings = (_option, _dayjsClass, dayjsFactory) => {
  dayjsFactory.HTML5_FMT = {
    DATE: 'YYYY-MM-DD',
    TIME: 'HH:mm'
  }
}

dayjs.extend(formatStrings)

Object.defineProperties(Vue.prototype, {
  $date: {
    get () {
      return dayjs
    }
  }
})

Vue.dayjs = dayjs
