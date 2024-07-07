import dayjs from 'dayjs'

import 'dayjs/locale/en-gb'
import 'dayjs/locale/de'
import 'dayjs/locale/de-ch'
import 'dayjs/locale/fr'
import 'dayjs/locale/fr-ch'
import 'dayjs/locale/it'
import 'dayjs/locale/it-ch'

import utc from 'dayjs/plugin/utc'
import customParseFormat from 'dayjs/plugin/customParseFormat'
import objectSupport from 'dayjs/plugin/objectSupport'
import localizedFormat from 'dayjs/plugin/localizedFormat'
import isBetween from 'dayjs/plugin/isBetween'
import isSameOrBefore from 'dayjs/plugin/isSameOrBefore'
import isSameOrAfter from 'dayjs/plugin/isSameOrAfter'
import duration from 'dayjs/plugin/duration'
import formatDatePeriod from './dayjs/formatDatePeriod.js'
import timezone from 'dayjs/plugin/timezone'

dayjs.extend(utc)
dayjs.extend(timezone)
dayjs.extend(customParseFormat)
dayjs.extend(objectSupport)
dayjs.extend(localizedFormat)
dayjs.extend(isBetween)
dayjs.extend(isSameOrBefore)
dayjs.extend(isSameOrAfter)
dayjs.extend(duration)
dayjs.extend(formatDatePeriod)

export const dayjsLocaleMap = {
  de: 'de-ch',
  en: 'en-gb',
  it: 'it-ch',
  fr: 'fr-ch',
}

/**
 * @typedef {import('dayjs').Dayjs} Dayjs
 * @property {Dayjs} utc
 * @property {Dayjs} tz
 */
export default dayjs
