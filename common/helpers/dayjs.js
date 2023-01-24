import dayjs from 'dayjs'

import 'dayjs/locale/de'
import 'dayjs/locale/de-ch'
import 'dayjs/locale/fr'
import 'dayjs/locale/it'

import utc from 'dayjs/plugin/utc'
import customParseFormat from 'dayjs/plugin/customParseFormat'
import localizedFormat from 'dayjs/plugin/localizedFormat'
import isBetween from 'dayjs/plugin/isBetween'
import duration from 'dayjs/plugin/duration'

dayjs.extend(utc)
dayjs.extend(customParseFormat)
dayjs.extend(localizedFormat)
dayjs.extend(isBetween)
dayjs.extend(duration)

export default dayjs
