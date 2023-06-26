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
import localizedFormat from 'dayjs/plugin/localizedFormat'
import isBetween from 'dayjs/plugin/isBetween'
import duration from 'dayjs/plugin/duration'
import formatDatePeriod from './dayjs/formatDatePeriod.js'

dayjs.extend(utc)
dayjs.extend(customParseFormat)
dayjs.extend(localizedFormat)
dayjs.extend(isBetween)
dayjs.extend(duration)
dayjs.extend(formatDatePeriod)

export default dayjs
