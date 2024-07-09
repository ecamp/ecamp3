import { toTime, roundTimeUpToNextQuarterHour } from '@/helpers/vCalendarDragAndDrop.js'

/**
 *
 * @param enabled {Ref<boolean>} drag & drop is disabled if enabled=false
 * @param update {(scheduleEntry: object, startTime: number, endTime: number) => void} callback for update actions
 * @param maxTimestamp {Ref<number>}  maximum allowed end timestamp (calendar end)
 * @returns
 */
export function useDragAndDropResize(enabled, update, maxTimestamp) {
  /**
   * internal data (not exposed)
   */

  // existing entry that is being resized
  let resizedEntry = null

  // original end time of entry which is being resized
  let originalEndTimestamp = null

  /**
   * internal methods
   */

  // resize an entry (existing or new placeholder)
  const resizeEntry = (entry, mouse) => {
    const newEndTimestamp = roundTimeUpToNextQuarterHour(mouse)

    if (
      newEndTimestamp <= maxTimestamp.value &&
      newEndTimestamp - entry.startTimestamp > 0
    ) {
      // TODO review: Here we're changing the store value directly.
      entry.endTimestamp = newEndTimestamp
    }

    entry.isResizing = true
  }

  const clear = () => {
    if (resizedEntry) {
      resizedEntry.isResizing = false
    }

    resizedEntry = null
    originalEndTimestamp = null
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
    if (!event.filterMatch) return

    resizedEntry = event
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
