/**
 *
 * @param ref(bool) enabled   false disables click detection
 * @param int threshold       max. mouse movement to still detect as a click
 * @returns
 */
export default function useClickDetector(enabled = true, threshold = 5, onClick = null) {
  /**
   * internal data (not exposed)
   */

  // coordinates of mouse down event
  let startX = null
  let startY = null

  /**
   * internal methods
   */
  function cancelClick() {
    startX = null
    startY = null
  }

  // returns true if still within defined threshold
  function withinThreshold(nativeEvent) {
    return (
      Math.abs(nativeEvent.x - startX) < threshold &&
      Math.abs(nativeEvent.y - startY) < threshold
    )
  }

  /**
   * exposed methods
   */
  const entryMouseDown = ({ nativeEvent }) => {
    if (!enabled.value) {
      return
    }

    startX = nativeEvent.x
    startY = nativeEvent.y
  }

  const entryMouseMove = ({ nativeEvent }) => {
    if (startX === null) {
      return
    }

    if (!withinThreshold(nativeEvent)) {
      // abort click if movement is larger than threshold
      cancelClick()
    }
  }

  const entryMouseUp = ({ event, nativeEvent }) => {
    if (startX === null) {
      return
    }

    if (!withinThreshold(nativeEvent)) {
      cancelClick()
      return
    }

    // Click middle button opens new tab
    if (nativeEvent.button === 1) {
      // emit('openEntry', event, true)
    } else if (nativeEvent.button === 0) {
      // Left click while holding cmd/ctrl opens new tab
      if (nativeEvent.metaKey || nativeEvent.ctrlKey) {
        // emit('openEntry', event, true)
      } else {
        // emit('openEntry', event, false)
      }

      // onClick callback
      if (onClick !== null) {
        onClick(event)
      }
    }

    cancelClick()
  }

  return {
    vCalendarListeners: {
      'mousedown:event': entryMouseDown,
      'mousemove:event': entryMouseMove,
      'mouseup:event': entryMouseUp,
    },
  }
}
