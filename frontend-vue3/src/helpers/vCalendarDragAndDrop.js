/**
 * helpers for VCalendar DragAndDrop composables
 */

const ONE_MINUTE_IN_MILLISECONDS = 60 * 1000
const ONE_HOUR_IN_MILLISECONDS = 60 * ONE_MINUTE_IN_MILLISECONDS
const ONE_DAY_IN_MILLISECONDS = 24 * ONE_HOUR_IN_MILLISECONDS
const QUARTER_HOUR_IN_MILLISECONDS = 15 * ONE_MINUTE_IN_MILLISECONDS

// helper function to convert Vuetify day & time object into timestamp
const toTime = (tms) => {
  return new Date(tms.year, tms.month - 1, tms.day, tms.hour, tms.minute).getTime()
}

const roundTimeToNearestQuarterHour = (time) => {
  return Math.round(time / QUARTER_HOUR_IN_MILLISECONDS) * QUARTER_HOUR_IN_MILLISECONDS
}

const roundTimeUpToNextQuarterHour = (time) => {
  return Math.ceil(time / QUARTER_HOUR_IN_MILLISECONDS) * QUARTER_HOUR_IN_MILLISECONDS
}

export {
  toTime,
  roundTimeToNearestQuarterHour,
  roundTimeUpToNextQuarterHour,
  ONE_MINUTE_IN_MILLISECONDS,
  ONE_HOUR_IN_MILLISECONDS,
  ONE_DAY_IN_MILLISECONDS,
}
