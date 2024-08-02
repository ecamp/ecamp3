import dayjs from '../dayjs'
import { HTML5_FMT } from '../dateFormat'

const REGEX_WITH_COLON = /([0-9]{1,2})[.:,\-;_hH]?([0-9]{1,2})?/
const REGEX_WITHOUT_COLON = /([0-9]{1,2})([0-9]{2})/

function matchDigitGroups(input) {
  if (input.match(/^\d{1,2}$/)) {
    return [input, '0']
  }
  const matchWithoutColon = input.match(REGEX_WITHOUT_COLON)
  if (matchWithoutColon) {
    return matchWithoutColon.slice(1)
  }
  const matchWithColon = input.match(REGEX_WITH_COLON)
  if (matchWithColon) {
    return matchWithColon.slice(1)
  }
  return []
}

export default (val) => {
  const stringVal = `${val}`
  let valIgnoringLeadingZero = stringVal.replace(/^0*?([\d]{1,2}):/, '$1:')
  const parsedDateTime = dayjs.utc(valIgnoringLeadingZero, 'LT')
  const formatted = parsedDateTime.format('LT')
  if (!formatted.startsWith('0') && valIgnoringLeadingZero.match(/^0\d/)) {
    valIgnoringLeadingZero = valIgnoringLeadingZero.slice(1)
  }
  const isValid = parsedDateTime.isValid() && formatted === valIgnoringLeadingZero

  if (isValid) {
    return { parsedDateTime, isValid }
  }

  const digitGroups = matchDigitGroups(stringVal)
  if (!digitGroups || digitGroups.length < 2) {
    return { parsedDateTime, isValid }
  }

  const hours = digitGroups[0]
  const minutes = digitGroups[1]
  const fuzzyMatchedTime = dayjs.utc(`${hours}:${minutes}`, HTML5_FMT.TIME)
  const fuzzyMatchedTimeToday = dayjs.utc(
    fuzzyMatchedTime.format(HTML5_FMT.TIME),
    HTML5_FMT.TIME
  )
  if (hours > 23 || minutes > 59) {
    return { parsedDateTime, isValid: false }
  }
  return { parsedDateTime: fuzzyMatchedTimeToday, isValid: fuzzyMatchedTime.isValid() }
}
