import { toTime, roundTimeUp, roundTimeDown } from '@/helpers/vCalendarDragAndDrop.js'

/**
 *
 * @param ref(bool) enabled   drag & drop is disabled if enabled=false
 * @param int threshold       min. mouse movement needed to detect drag & drop
 * @returns
 */
export default function useDragAndDrop (enabled, update) {
  /**
   * internal data (not exposed)
   */

  // existing entry that is being resized
  let resizedEntry = null

  // original start time of entry which is being resized
  let originalStartTime = null

  // original end time of entry which is being resized
  let originalEndTime = null

  /**
   * internal methods
   */

  // resize an entry (existing or new placeholder)
  const resizeEntry = (entry, mouse) => {
    const mouseRounded = roundTimeUp(mouse)
    const min = Math.min(mouseRounded, roundTimeDown(originalStartTime))
    const max = Math.max(mouseRounded, roundTimeDown(originalStartTime))

    // TODO review: Here we're changing the store value directly.
    entry.startTime = min
    entry.endTime = max
  }

  const clear = () => {
    resizedEntry = null
    originalEndTime = null
    originalStartTime = null
  }

  /**
   * exposed methods
   */

  // triggered when mouse is being moved in calendar (independent whether drag & drop is ongoing or not)
  const timeMouseMove = (tms, nativeEvent) => {
    if (!enabled.value) { return }

    if (resizedEntry) {
      const mouseTime = toTime(tms)
      resizeEntry(resizedEntry, mouseTime)
    }
  }

  // triggered with MouseUp Event anywhere in the calendar
  const timeMouseUp = () => {
    if (!enabled.value) { return }

    if (resizedEntry && resizedEntry.endTime !== originalEndTime) {
      // existing entry was resized --> save to API
      update(resizedEntry, resizedEntry.startTime, resizedEntry.endTime)
    }

    clear()
  }

  // treat mouseleave as a mouseUp event (finish operation and save last known status)
  const nativeMouseLeave = () => {
    timeMouseUp()
  }

  // start resize operation (needs to be called manually from resize handle)
  const startResize = (event) => {
    resizedEntry = event
    originalStartTime = event.startTime
    originalEndTime = event.endTime
  }

  return {
    vCalendarListeners: {
      'mousemove:time': timeMouseMove,
      'mouseup:time': timeMouseUp
    },
    nativeMouseLeave,
    startResize
  }
}
