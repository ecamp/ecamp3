/**
 * helpers for VCalendar DragAndDrop composables
 */

const ONE_MINUTE = 60 * 1000
const ONE_HOUR = 60 * ONE_MINUTE
const ONE_DAY = 24 * ONE_HOUR

const roundToMinutes = 15 // minutes
const roundToMilliseconds = roundToMinutes * ONE_MINUTE

// helper function to convert Vuetify day & time object into timestamp
const toTime = (tms) => {
  return new Date(tms.year, tms.month - 1, tms.day, tms.hour, tms.minute).getTime()
}

const roundTime = (time) => {
  return Math.round(time / roundToMilliseconds) * roundToMilliseconds
}

const roundTimeDown = (time) => {
  const roundTo = 15 // minutes
  const roundDownTime = roundTo * 60 * 1000

  return time - (time % roundDownTime)
}

const roundTimeUp = (time) => {
  const roundTo = 15 // minutes
  const roundDownTime = roundTo * 60 * 1000

  return time + (roundDownTime - (time % roundDownTime))
}

const minMaxTime = (start, end) => {
  const startCeil = roundTimeUp(end)
  return {
    min: Math.min(startCeil, roundTimeDown(start)),
    max: Math.max(startCeil, roundTimeDown(start)),
  }
}

export {
  toTime,
  roundTime,
  roundTimeDown,
  roundTimeUp,
  minMaxTime,
  ONE_MINUTE,
  ONE_HOUR,
  ONE_DAY,
}
