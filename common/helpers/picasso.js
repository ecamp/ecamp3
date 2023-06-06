import countBy from 'lodash/countBy'
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
 * @param firstDay string description of the first day displayed. Is used to make sure that schedule entries starting on
 * this day may not be assigned to the preceding day, because that would mean they would become invisible on the picasso
 * @param lastDay string description of the last day displayed. Is used to make sure that schedule entries ending on
 * this day may not be assigned to the following day, because that would mean they would become invisible on the picasso
 * @param timeBucketSize size of the time buckets into which the schedule entry boundaries are quantized, in hours
 * @returns {{bedtime: number, getUpTime: number}}
 */
export function calculateBedtime(scheduleEntries, dayjs, firstDay, lastDay, timeBucketSize = 1) {
  if (!scheduleEntries.length) return { bedtime: 24, getUpTime: 0 }

  const scheduleEntryBoundaries = scheduleEntries.flatMap((scheduleEntry) => [
    bucketize(dayjs.utc(scheduleEntry.start), timeBucketSize),
    bucketize(dayjs.utc(scheduleEntry.end), timeBucketSize),
  ])
  const activeTimes = countBy(scheduleEntryBoundaries, undefined)
  const sortedActiveTimes = sortBy(Object.keys(activeTimes).map(activeTime => parseFloat(activeTime)))
  // Use two copies of the active times, to work around problems coming from the circularity of the day's hours
  const activeTimesAsc = sortedActiveTimes.concat(sortedActiveTimes.map(time => time + 24))

  // The first and last day on our picasso impose some constraints on the range of bedtimes we can choose.
  const { earliestBedtime, latestGetUpTime } =
    bedtimeConstraintsFromFirstAndLastDay(scheduleEntries, firstDay, lastDay, dayjs, timeBucketSize)

  // Times during the night which could be sleep times, in descending likeliness
  const sleepTimeCandidates = [28, 26, 29, 24, 30]
  let bedtime = 24
  let getUpTime = 24
  sleepTimeCandidates
    .filter(time => time <= latestGetUpTime && time >= earliestBedtime)
    .forEach(time => {
      if (activeTimes[time % 24]) return
      const gapStart = activeTimesAsc.findLast(activeTime => activeTime <= time)
      const gapEnd = activeTimesAsc.find(activeTime => activeTime >= time)
      if ((gapEnd - gapStart) > (getUpTime - bedtime)) {
        bedtime = gapStart
        getUpTime = gapEnd
      }
    })

  if (getUpTime - bedtime < 2) return { bedtime: 24, getUpTime: 0 }

  // Show an hour more than strictly necessary, to make sure that schedule entries starting late
  // in the evening and/or ending early in the morning are still clearly visible on both ends.
  return {
    bedtime: bedtime + 1,
    getUpTime: getUpTime - 24 - 1 // convert getUpTime into the morning hours
  }
}

function bedtimeConstraintsFromFirstAndLastDay(scheduleEntries, firstDay, lastDay, dayjs, timeBucketSize) {
  // The start of the very first schedule entry on the first day (if any) must always be displayed on the first day.
  // We must make sure that our calculated "get up time" lies before this, so we do not accidentally hide a
  // schedule entry.
  const latestGetUpTime = earliestScheduleEntryStartOnFirstDay(scheduleEntries, firstDay, dayjs, timeBucketSize)

  // Similarly, the end of the very last schedule entry end must always be displayed on the last day.
  // So we must make sure that our calculated "go to bed time" lies after this.
  const earliestBedtime = latestScheduleEntryEndOnLastDay(scheduleEntries, lastDay, dayjs, timeBucketSize)

  return {
    earliestBedtime: earliestBedtime === null ? 0 : earliestBedtime,
    latestGetUpTime: latestGetUpTime === null ? 36 : latestGetUpTime + 24
  }
}

function earliestScheduleEntryStartOnFirstDay(scheduleEntries, firstDay, dayjs, timeBucketSize) {
  const firstScheduleEntry = minBy(scheduleEntries, (scheduleEntry) => dayjs.utc(scheduleEntry.start).unix())
  if (!isOnDay(firstScheduleEntry.start, firstDay, dayjs)) return null
  return bucketize(dayjs.utc(firstScheduleEntry.start), timeBucketSize)
}

function latestScheduleEntryEndOnLastDay(scheduleEntries, lastDay, dayjs, timeBucketSize) {
  const lastScheduleEntry = maxBy(scheduleEntries, (scheduleEntry) => dayjs.utc(scheduleEntry.end).unix())
  if (!isOnDay(lastScheduleEntry.end, lastDay, dayjs)) return null
  return bucketize(dayjs.utc(lastScheduleEntry.end), timeBucketSize)
}

function isOnDay(scheduleEntryTime, dayStart, dayjs) {
  return dayjs.utc(scheduleEntryTime).format('YYYY-MM-DD') === dayjs.utc(dayStart).format('YYYY-MM-DD')
}

function bucketize(boundary, timeBucketSize) {
  return roundDownTo(boundary.hour() + boundary.minute() / 60, timeBucketSize) % 24
}

function roundDownTo(number, stepSize) {
  return Math.floor(number / stepSize) * stepSize
}
