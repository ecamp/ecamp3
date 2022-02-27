import { toTime, roundTimeUp, roundTimeDown } from '@/helpers/vCalendarDragAndDrop.js'

/**
 *
 * @param ref(bool) enabled   drag & drop is disabled if enabled=false
 * @param int threshold       min. mouse movement needed to detect drag & drop
 * @returns
 */
export default function useDragAndDrop (enabled, createEntry) {
  /**
   * internal data (not exposed)
   */

  // timestamp of mouse location when drag & drop event started
  let mouseStartTime = null

  // temporary placeholder for new schedule entry, when created via drag & drop
  let newEntry = null

  // true if mousedown event was detected on an entry/event
  let entryWasClicked = false

  /**
   * internal methods
   */

  const clear = () => {
    newEntry = null
    mouseStartTime = null
    entryWasClicked = false
  }

  // this creates a placeholder for a new schedule entry and make it resizable
  const createNewEntry = (mouse) => {
    newEntry = {
      startTime: roundTimeDown(mouse),
      endTime: roundTimeDown(mouse) + 15
    }
  }

  // resize placeholder entry
  const resizeEntry = (entry, mouse) => {
    const mouseRounded = roundTimeUp(mouse)
    const min = Math.min(mouseRounded, roundTimeDown(mouseStartTime))
    const max = Math.max(mouseRounded, roundTimeDown(mouseStartTime))

    entry.startTime = min
    entry.endTime = max
  }

  /**
   * exposed methods
   */

  // triggered with MouseDown event on a calendar entry
  const entryMouseDown = ({ event: entry, timed, nativeEvent }) => {
    if (!enabled.value) { return }

    // cancel drag & drop if button is not left button
    if (nativeEvent.button !== 0) { return }

    if (entry && timed) {
      entryWasClicked = true
    }
  }

  // triggered with MouseDown event anywhere on the calendar (independent of clicking on entry or not)
  const timeMouseDown = (tms, nativeEvent) => {
    if (!enabled.value) { return }

    // cancel drag & drop if button is not left button
    if (nativeEvent.button !== 0) { return }

    if (!entryWasClicked) {
      // No entry is being dragged --> create a placeholder for a new schedule entry
      const mouseTime = toTime(tms)
      mouseStartTime = mouseTime
      createNewEntry(mouseTime)
    }
  }

  // triggered when mouse is being moved in calendar (independent whether drag & drop is ongoing or not)
  const timeMouseMove = (tms, nativeEvent) => {
    if (!enabled.value) { return }

    if (newEntry) {
      // resize placeholder
      const mouseTime = toTime(tms)
      resizeEntry(newEntry, mouseTime)

      createEntry(newEntry.startTime, newEntry.endTime, false)
    }
  }

  // triggered with MouseUp Event anywhere in the calendar
  const timeMouseUp = () => {
    if (!enabled.value) { return }

    if (newEntry) {
      // placeholder for new schedule entry was created --> open dialog to create new activity
      createEntry(newEntry.startTime, newEntry.endTime, true)
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
      'mouseup:time': timeMouseUp
    },
    nativeMouseLeave
  }
}
