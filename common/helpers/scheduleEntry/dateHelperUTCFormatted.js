import dayjs from '../dayjs.js'
import i18n from '@/plugins/i18n'

function dateShort(dateTimeString){
  return dayjs.utc(dateTimeString).format(i18n.tc('global.datetime.dateShort'))
}

function dateLong(dateTimeString){
  return dayjs.utc(dateTimeString).format(i18n.tc('global.datetime.dateLong'))
}

function hourShort(dateTimeString){
  return dayjs.utc(dateTimeString).format(i18n.tc('global.datetime.hourShort'))
}


export {
  dateShort,
  dateLong,
  hourShort
}
