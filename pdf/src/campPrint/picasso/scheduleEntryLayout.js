// This file contains an algorithm for automatically arranging schedule entries in terms
// of horizontal placement, such that they do not overlap with each other.
// Taken and adapted from Vuetify 2's v-calendar event overlap logic:
// https://github.com/vuetifyjs/vuetify/blob/v2-stable/packages/vuetify/src/components/VCalendar/modes/column.ts

import dayjs from '../../../common/helpers/dayjs.js'

const FULL_WIDTH = 100

function getVisuals(events) {
  const visuals = events.map((event) => ({
    id: event.id,
    columnCount: 0,
    column: 0,
    left: 0,
    width: 100,
    startTimestamp: dayjs.utc(event.start).unix(),
    endTimestamp: dayjs.utc(event.end).unix(),
  }))

  visuals.sort((a, b) => {
    return a.startTimestamp - b.startTimestamp || b.endTimestamp - a.endTimestamp
  })

  return visuals
}

function hasOverlap(s0, e0, s1, e1) {
  return !(s0 >= e1 || e0 <= s1)
}

function setColumnCount(groups) {
  groups.forEach((group) => {
    group.visuals.forEach((groupVisual) => {
      groupVisual.columnCount = groups.length
      groupVisual.left = (groupVisual.column * FULL_WIDTH) / groupVisual.columnCount
      groupVisual.width = FULL_WIDTH / groupVisual.columnCount
    })
  })
}

function getOpenGroup(groups, eventStart, eventEnd) {
  for (let i = 0; i < groups.length; i++) {
    const group = groups[i]
    let intersected = false

    if (hasOverlap(eventStart, eventEnd, group.start, group.end)) {
      for (let k = 0; k < group.visuals.length; k++) {
        const groupVisual = group.visuals[k]
        const groupStart = groupVisual.startTimestamp
        const groupEnd = groupVisual.endTimestamp

        if (hasOverlap(eventStart, eventEnd, groupStart, groupEnd)) {
          intersected = true
          break
        }
      }
    }

    if (!intersected) {
      return i
    }
  }

  return -1
}

/**
 * Calculates left and width for each schedule entry, such that no two schedule entries overlap visually.
 * For this, schedule entries are sorted by ascending start ascendingly (and descending end time for ties),
 * and any sets of overlapping schedule entries are divided into groups. The groups can then be displayed
 * besides each other, because the groups are chosen such that no two schedule entries in the same group overlap.
 * @param scheduleEntries list of schedule entries to arrange
 * @returns list of objects with information about the spatial arrangement
 */
export function arrange(scheduleEntries) {
  let groups = []
  let min = -1
  let max = -1
  const visuals = getVisuals(scheduleEntries)

  visuals.forEach((visual) => {
    const eventStart = visual.startTimestamp
    const eventEnd = visual.endTimestamp

    if (groups.length > 0 && !hasOverlap(eventStart, eventEnd, min, max)) {
      setColumnCount(groups)
      groups = []
      min = max = -1
    }

    let targetGroup = getOpenGroup(groups, eventStart, eventEnd)

    if (targetGroup === -1) {
      targetGroup = groups.length

      groups.push({ start: eventStart, end: eventEnd, visuals: [] })
    }

    const target = groups[targetGroup]
    target.visuals.push(visual)
    target.start = Math.min(target.start, eventStart)
    target.end = Math.max(target.end, eventEnd)

    visual.column = targetGroup

    if (min === -1) {
      min = eventStart
      max = eventEnd
    } else {
      min = Math.min(min, eventStart)
      max = Math.max(max, eventEnd)
    }
  })

  setColumnCount(groups)

  return visuals
}
