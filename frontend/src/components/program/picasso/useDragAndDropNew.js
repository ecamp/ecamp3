import { toTime, roundTimeToNearestQuarterHour } from '@/helpers/vCalendarDragAndDrop.js'

/**
 *
 * @param enabled {Ref<boolean>} drag & drop is disabled if enabled=false
 * @param createEntry {(startTime:number, endTime:number, finished:boolean) => void}
 * @returns
 */
export function useDragAndDropNew(enabled, createEntry) {
  /**
   * internal data (not exposed)
   */

  // timestamp of mouse location when drag & drop event started
  let mouseStartTimestamp = null

  // temporary placeholder for new schedule entry, when created via drag & drop
  let newEntry = null

  // true if mousedown event was detected on an entry/event
  let entryWasClicked = false

  /**
   * internal methods
   */

  const clear = () => {
    newEntry = null
    mouseStartTimestamp = null
    entryWasClicked = false
  }

  // this creates a placeholder for a new schedule entry and make it resizable
  const createNewEntry = (time) => {
    newEntry = {
      startTimestamp: time,
      endTimestamp: time,
    }
  }

  // resize placeholder entry
  const resizeEntry = (entry, mouse) => {
    const dragStart = mouseStartTimestamp
    const dragEnd = roundTimeToNearestQuarterHour(mouse)

    const minTimestamp = Math.min(dragStart, dragEnd)
    const maxTimestamp = Math.max(dragStart, dragEnd)

    if (minTimestamp !== maxTimestamp) {
      entry.startTimestamp = minTimestamp
      entry.endTimestamp = maxTimestamp
    }
  }

  /**
   * exposed methods
   */

  // triggered with MouseDown event on a calendar entry
  const entryMouseDown = ({ event: entry, timed, nativeEvent }) => {
    if (!enabled.value) {
      return
    }

    // cancel drag & drop if button is not left button
    if (nativeEvent.button !== 0) {
      return
    }

    if (entry && timed) {
      entryWasClicked = true
    }
  }

  // triggered with MouseDown event anywhere on the calendar (independent of clicking on entry or not)
  const timeMouseDown = (tms, nativeEvent) => {
    if (!enabled.value) {
      return
    }

    // cancel drag & drop if button is not left button
    if (nativeEvent.button !== 0) {
      return
    }

    if (!entryWasClicked) {
      // No entry is being dragged --> create a placeholder for a new schedule entry
      const mouseTime = toTime(tms)
      mouseStartTimestamp = roundTimeToNearestQuarterHour(mouseTime)
      createNewEntry(mouseStartTimestamp)
    }
  }

  // triggered when mouse is being moved in calendar (independent whether drag & drop is ongoing or not)
  const timeMouseMove = (tms) => {
    if (!enabled.value) {
      return
    }

    if (newEntry) {
      // resize placeholder
      const mouseTime = toTime(tms)
      resizeEntry(newEntry, mouseTime)

      if (newEntry.endTimestamp - newEntry.startTimestamp > 0) {
        createEntry(newEntry.startTimestamp, newEntry.endTimestamp, false)
      }
    }
  }

  // triggered with MouseUp Event anywhere in the calendar
  const timeMouseUp = () => {
    if (!enabled.value) {
      return
    }

    if (newEntry && newEntry.endTimestamp - newEntry.startTimestamp > 0) {
      // placeholder for new schedule entry was created --> open dialog to create new activity
      createEntry(newEntry.startTimestamp, newEntry.endTimestamp, true)
    }

    clear()
  }

  // treat mouseleave as a mouseUp event (finish operation and save last known status)
  const nativeMouseLeave = () => {
    timeMouseUp()
  }

  return {
    vCalendarListeners: {
      'mousedown:event': entryMouseDown,
      'mousedown:time': timeMouseDown,
      'mousemove:time': timeMouseMove,
      'mouseup:time': timeMouseUp,
    },
    nativeMouseLeave,
  }
}
