import dayjs from './dayjs.js'
import { i18n } from '@/plugins/i18n' // this imports i18-plugin from "frontend" or from "print", depending on where the helper is used

function dateShort(dateTimeString) {
  return dayjs.utc(dateTimeString).format(i18n.tc('global.datetime.dateShort'))
}

function dateLong(dateTimeString) {
  return dayjs.utc(dateTimeString).format(i18n.tc('global.datetime.dateLong'))
}

function hourShort(dateTimeString) {
  return dayjs.utc(dateTimeString).format(i18n.tc('global.datetime.hourShort'))
}

function timeDurationShort(start, end) {
  const startTime = dayjs.utc(start)
  const endTime = dayjs.utc(end)
  const duration = dayjs(endTime.diff(startTime))
  return duration.format(i18n.tc('global.datetime.durationShort'))
}

// short format of dateTime range
// doesn't repeat end date if on the same day
function rangeShort(start, end) {
  let result = ''

  result += dateShort(start)
  result += ' '
  result += hourShort(start)

  result += ' - '

  if (dateShort(start) !== dateShort(end)) {
    result += dateShort(end)
    result += ' '
  }

  result += hourShort(end)

  return result
}

// format of date range
function dateRange(start, end) {
  if (dateLong(start) === dateLong(end)) {
    return dateLong(start)
  }
  return `${dateShort(start)} - ${dateLong(end)}`
}

export { dateShort, dateLong, timeDurationShort, hourShort, dateRange, rangeShort }
