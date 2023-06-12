import minBy from 'lodash/minBy'
import maxBy from 'lodash/maxBy'
import sortBy from 'lodash/sortBy'

/**
 * Splits a set of days into pages, such that all pages contain a similar number of days.
 *
 * @param days {array} set of days to split into pages
 * @param maxDaysPerPage {number} maximum number of days to put on one page
 * @returns {array} list of pages, each containing a list of the days on the page
 */
export function splitDaysIntoPages(days, maxDaysPerPage) {
  const numberOfDays = days.length
  const numberOfPages = Math.ceil(numberOfDays / maxDaysPerPage)
  const daysPerPage = Math.floor(numberOfDays / numberOfPages)
  const numLargerPages = numberOfDays % numberOfPages
  let nextUnassignedDayIndex = 0
  if (isNaN(numberOfPages)) return []

  return [...Array(numberOfPages).keys()].map((i) => {
    const isLargerPage = i < numLargerPages
    const numDaysOnCurrentPage = daysPerPage + (isLargerPage ? 1 : 0)
    const firstDayIndex = nextUnassignedDayIndex
    nextUnassignedDayIndex = firstDayIndex + numDaysOnCurrentPage

    return days.filter((day, index) => {
      return index >= firstDayIndex && index < nextUnassignedDayIndex
    })
  })
}

/**
 * Finds the largest consecutive time period during the night, in which no schedule entries start or end.
 * This time period can be treated as the common "bedtime" in the camp, during which
 * the people in the camp are not active. This time period can be safely hidden on the picasso.
 *
 * @param scheduleEntries set of schedule entries to consider
 * @param dayjs a dayjs helper object, needed to do time calculations
 * @param firstDayStart dayjs object describing the start of the first day displayed. Is used to make sure that schedule
 * entries starting on this day may not be assigned to the preceding day, because that would mean they would become
 * invisible on the picasso
 * @param lastDayEnd dayjs object describing the end of the last day displayed. Is used to make sure that schedule
 * entries ending on this day may not be assigned to the following day, because that would mean they would become
 * invisible on the picasso
 * @param timeBucketSize size of the time buckets into which the schedule entry boundaries are quantized, in hours
 * @returns {{bedtime: number, getUpTime: number}}
 */
export function calculateBedtime(
  scheduleEntries,
  dayjs,
  firstDayStart,
  lastDayEnd,
  timeBucketSize = 1
) {
  const scheduleEntryBounds = getScheduleEntryBounds(
    scheduleEntries,
    dayjs,
    firstDayStart.unix(),
    lastDayEnd.unix()
  )
  if (!scheduleEntryBounds.length) return { bedtime: 24, getUpTime: 0 }

  const gaps = scheduleEntryBounds.reduce((gaps, current, index) => {
    if (index === 0) return gaps
    const previous = scheduleEntryBounds[index - 1]
    const duration = current.hours - previous.hours
    if (duration === 0) return gaps
    gaps.push({
      start: previous.hours,
      end: current.hours,
      duration,
    })
    return gaps
  }, [])

  // The first and last day on our picasso impose some constraints on the range of bedtimes we can choose.
  const { earliestBedtime, latestGetUpTime } = bedtimeConstraintsFromFirstAndLastDay(
    scheduleEntryBounds,
    firstDayStart,
    lastDayEnd,
    dayjs,
    timeBucketSize
  )

  const largestBedtimeGap = maxBy(
    gaps.filter((gap) => {
      // Prevent bedtimes which would hide some schedule entry on the first or last day
      if (gap.start < earliestBedtime || gap.end > latestGetUpTime) return false
      // Prevent bedtimes which are not during the night
      if (gap.start > 30 || gap.end < 24) return false
      return true
    }),
    (gap) => gap.duration
  )

  return {
    bedtime: optimalBedtime(largestBedtimeGap, scheduleEntryBounds, timeBucketSize),
    getUpTime:
      optimalGetUpTime(largestBedtimeGap, scheduleEntryBounds, timeBucketSize) - 24,
  }
}

function getScheduleEntryBounds(
  scheduleEntries,
  dayjs,
  firstDayStartTimestamp,
  lastDayEndTimestamp
) {
  return sortBy(
    scheduleEntries.flatMap((scheduleEntry) => {
      const result = []
      const start = dayjs.utc(scheduleEntry.start)
      if (start.unix() >= firstDayStartTimestamp && start.unix() <= lastDayEndTimestamp) {
        const hours = start.hour() + start.minute() / 60
        result.push(
          { hours, time: start, type: 'start' },
          // Add a copy 24 hours later, to simplify working with the circular characteristics of daytimes
          // TODO can we be more efficient, e.g. by only putting a copy of the earliest bound 24 hours later?
          { hours: hours + 24, time: start.add(24, 'hours'), type: 'start' }
        )
      }

      const end = dayjs.utc(scheduleEntry.end)
      if (end.unix() >= firstDayStartTimestamp && end.unix() <= lastDayEndTimestamp) {
        const hours = end.hour() + end.minute() / 60
        result.push(
          { hours, time: end, type: 'end' },
          // Add a copy 24 hours later, to simplify working with the circular characteristics of daytimes
          { hours: hours + 24, time: end.add(24, 'hours'), type: 'end' }
        )
      }
      return result
    }),
    (bound) => bound.hours
  )
}

function bedtimeConstraintsFromFirstAndLastDay(
  scheduleEntryBounds,
  firstDayStart,
  lastDayEnd
) {
  // The start of the very first schedule entry on the first day (if any) must always be displayed on the first day.
  // We must make sure that our calculated "get up time" lies before this, so we do not accidentally hide a
  // schedule entry.
  const latestGetUpTime = earliestScheduleEntryBoundOnFirstDay(
    scheduleEntryBounds,
    firstDayStart
  )

  // Similarly, the end of the very last schedule entry end must always be displayed on the last day.
  // So we must make sure that our calculated "go to bed time" lies after this.
  const earliestBedtime = latestScheduleEntryBoundOnLastDay(
    scheduleEntryBounds,
    lastDayEnd.subtract(1, 'second')
  )

  return {
    earliestBedtime: earliestBedtime === undefined ? 0 : earliestBedtime,
    latestGetUpTime: latestGetUpTime === undefined ? 48 : latestGetUpTime + 24,
  }
}

function earliestScheduleEntryBoundOnFirstDay(scheduleEntryBounds, firstDayStart) {
  const earliestBound = minBy(scheduleEntryBounds, (bound) => bound.time.unix())
  if (
    earliestBound.hours < 24 &&
    earliestBound.time.diff(firstDayStart, 'minute') / 60 < 24
  ) {
    return earliestBound.hours
  }
  return undefined
}

function latestScheduleEntryBoundOnLastDay(scheduleEntryBounds, lastDayEnd) {
  const latestBound = maxBy(scheduleEntryBounds, (bound) => bound.time.unix())
  if (latestBound.hours < 24 && lastDayEnd.diff(latestBound.time, 'minute') / 60 < 24) {
    return latestBound.hours
  }
  return undefined
}

function optimalBedtime(gap, scheduleEntryBounds, timeBucketSize) {
  const bedtime = Math.ceil(gap.start / timeBucketSize) * timeBucketSize
  if (
    scheduleEntryBounds.some(
      (bound) =>
        bound.type === 'start' &&
        bound.hours <= bedtime &&
        bound.hours > bedtime - timeBucketSize / 2
    )
  ) {
    // There exists a schedule entry which starts at the bedtime or less than half a time bucket before it.
    // Move the bedtime later, so that this schedule entry is still clearly visible.
    return bedtime + timeBucketSize
  }
  // There is already a large enough margin, or there are no schedule entries starting around the bedtime.
  return bedtime
}

function optimalGetUpTime(gap, scheduleEntryBounds, timeBucketSize) {
  const getUpTime = Math.floor(gap.end / timeBucketSize) * timeBucketSize

  if (
    scheduleEntryBounds.some(
      (bound) =>
        bound.type === 'end' &&
        bound.hours >= getUpTime &&
        bound.hours < getUpTime + timeBucketSize / 2
    )
  ) {
    // There exists a schedule entry which ends at the getUpTime or less than half a time bucket after it.
    // Move the getUpTime later, so that this schedule entry is still clearly visible.
    return getUpTime - timeBucketSize
  }
  // There is already a large enough margin, or there are no schedule entries ending around the getUpTime.
  return getUpTime
}
