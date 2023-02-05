import { toTime, minMaxTime } from '@/helpers/vCalendarDragAndDrop.js'

/**
 *
 * @param enabled {Ref<boolean>} drag & drop is disabled if enabled=false
 * @param update {(scheduleEntry: object, startTime: number, endTime: number) => void} callback for update actions
 * @param minTimestamp {number}  minimum allowed start timestamp (calendar start)
 * @param maxTimestamp {number}  maximum allowed end timestamp (calendar end)
 * @returns
 */
export function useDragAndDropResize(enabled, update, minTimestamp, maxTimestamp) {
  /**
   * internal data (not exposed)
   */

  // existing entry that is being resized
  let resizedEntry = null

  // original start time of entry which is being resized
  let originalStartTimestamp = null

  // original end time of entry which is being resized
  let originalEndTimestamp = null

  /**
   * internal methods
   */

  // resize an entry (existing or new placeholder)
  const resizeEntry = (entry, mouse) => {
    const { min: newStart, max: newEnd } = minMaxTime(originalStartTimestamp, mouse)

    if (newStart >= minTimestamp && newEnd <= maxTimestamp && newEnd - newStart > 0) {
      // TODO review: Here we're changing the store value directly.
      entry.startTimestamp = newStart
      entry.endTimestamp = newEnd
    }
  }

  const clear = () => {
    resizedEntry = null
    originalEndTimestamp = null
    originalStartTimestamp = null
  }

  /**
   * exposed methods
   */

  // triggered when mouse is being moved in calendar (independent whether drag & drop is ongoing or not)
  const timeMouseMove = (tms) => {
    if (!enabled.value) {
      return
    }

    if (resizedEntry) {
      const mouseTime = toTime(tms)
      resizeEntry(resizedEntry, mouseTime)
    }
  }

  // triggered with MouseUp Event anywhere in the calendar
  const timeMouseUp = () => {
    if (!enabled.value) {
      return
    }

    if (resizedEntry && resizedEntry.endTimestamp !== originalEndTimestamp) {
      // existing entry was resized --> save to API
      update(resizedEntry, resizedEntry.startTimestamp, resizedEntry.endTimestamp)
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
    originalStartTimestamp = event.startTimestamp
    originalEndTimestamp = event.endTimestamp
  }

  return {
    vCalendarListeners: {
      'mousemove:time': timeMouseMove,
      'mouseup:time': timeMouseUp,
    },
    nativeMouseLeave,
    startResize,
  }
}
