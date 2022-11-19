import { toTime, minMaxTime, ONE_HOUR } from '@/helpers/vCalendarDragAndDrop.js'

/**
 *
 * @param enabled {Ref<boolean>} enabled   drag & drop is disabled if enabled=false
 * @param showReminder {(move?: boolean) => void} threshold       min. mouse movement needed to detect drag & drop
 * @returns
 */
export function useDragAndDropReminder(enabled, showReminder) {
  /**
   * internal data (not exposed)
   */

  // temporary placeholder for new schedule entry, when created via drag & drop
  let mouseStartTimestamp = null

  // true if mousedown event was detected on an entry/event
  let entryWasClicked = false

  /**
   * internal methods
   */

  const clear = () => {
    mouseStartTimestamp = null
    entryWasClicked = false
  }

  /**
   * exposed methods
   */

  // triggered with MouseDown event on a calendar entry
  const entryMouseDown = ({ event: entry, timed, nativeEvent }) => {
    if (enabled.value) {
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
    if (enabled.value) {
      return
    }

    // cancel drag & drop if button is not left button
    if (nativeEvent.button !== 0) {
      return
    }

    mouseStartTimestamp = toTime(tms)
  }

  // triggered when mouse is being moved in calendar (independent whether drag & drop is ongoing or not)
  const timeMouseMove = (tms) => {
    if (enabled.value || mouseStartTimestamp == null) {
      return
    }

    const { min, max } = minMaxTime(mouseStartTimestamp, toTime(tms))

    if (max - min >= ONE_HOUR) {
      showReminder(entryWasClicked)
    }
  }

  // triggered with MouseUp Event anywhere in the calendar
  const timeMouseUp = () => {
    if (enabled.value) {
      return
    }

    clear()
  }

  return {
    vCalendarListeners: {
      'mousedown:event': entryMouseDown,
      'mousedown:time': timeMouseDown,
      'mousemove:time': timeMouseMove,
      'mouseup:time': timeMouseUp,
    },
  }
}
