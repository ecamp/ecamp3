import dayjs from '../dayjs'

export default (val) => {
  let valIgnoringLeadingZero = val.replace(/^0*?([\d]{1,2}):/, '$1:')
  const parsedDateTime = dayjs.utc(valIgnoringLeadingZero, 'LT')
  const formatted = parsedDateTime.format('LT')
  if (!formatted.startsWith('0') && valIgnoringLeadingZero.match(/^0\d/)) {
    valIgnoringLeadingZero = valIgnoringLeadingZero.slice(1)
  }
  const isValid = parsedDateTime.isValid() && formatted === valIgnoringLeadingZero
  return { parsedDateTime, isValid }
}
