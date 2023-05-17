/**
 * helpers for VCalendar DragAndDrop composables
 */

const ONE_MINUTE = 60 * 1000
const ONE_HOUR = 60 * ONE_MINUTE
const ONE_DAY = 24 * ONE_HOUR
const QUARTER_HOUR = 15 * ONE_MINUTE

// helper function to convert Vuetify day & time object into timestamp
const toTime = (tms) => {
  return new Date(tms.year, tms.month - 1, tms.day, tms.hour, tms.minute).getTime()
}

const roundTimeToNearestQuarterHour = (time) => {
  return Math.round(time / QUARTER_HOUR) * QUARTER_HOUR
}

const roundTimeUpToNextQuarterHour = (time) => {
  return time + (QUARTER_HOUR - (time % QUARTER_HOUR))
}

export {
  toTime,
  roundTimeToNearestQuarterHour,
  roundTimeUpToNextQuarterHour,
  ONE_MINUTE,
  ONE_HOUR,
  ONE_DAY,
}
