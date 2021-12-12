/**
 *
 * @param bool editable     drag & drop is disabled if editable=false
 * @param int threshold     min. mouse movement needed to detect drag & drop
 * @returns
 */
export default function useDragAndDrop (editable, threshold, update, emit) {
  /**
   * internal data (not exposed)
   */

  // timestamp of mouse location when drag & drop event started
  let mouseStartTime = null

  // reference to dragged scheduleEntry (null = no drag & drop ongoing)
  let draggedEntry = null

  // offset between (dragged) entry startTime and mouse location
  let draggedEntryMouseOffset = null

  // existing entry that is being resized
  let resizedEntry = null

  // original end time of entry which is being resized
  let resizedEntryOldEndTime = null

  // temporary placeholder for new schedule entry, when created via drag & drop
  let newEntry = null

  // coordinates of mouse down event
  let startX = null
  let startY = null

  // true while drag & drop action is ongoing
  let dragging = false

  /**
   * internal methods
   */

  // returns true if still within defined threshold
  function withinThreshold (nativeEvent) {
    return (Math.abs(nativeEvent.x - startX) < threshold) && (Math.abs(nativeEvent.y - startY) < threshold)
  }

  // move an existing entry
  const moveDraggedEntry = (mouse) => {
    const start = draggedEntry.startTime
    const end = draggedEntry.endTime
    const duration = end - start

    const newStart = roundTimeDown((mouse - draggedEntryMouseOffset))
    const newEnd = newStart + duration

    // TODO review: Here we're changing the store value directly.
    draggedEntry.startTime = newStart
    draggedEntry.endTime = newEnd
  }

  // resize an entry (existing or new placeholder)
  const resizeEntry = (entry, mouse) => {
    const mouseRounded = roundTimeUp(mouse)
    const min = Math.min(mouseRounded, roundTimeDown(mouseStartTime))
    const max = Math.max(mouseRounded, roundTimeDown(mouseStartTime))

    // TODO review: Here we're changing the store value directly.
    entry.startTime = min
    entry.endTime = max
  }

  const clearResizedEntry = () => {
    resizedEntry = null
    resizedEntryOldEndTime = null
    mouseStartTime = null
  }

  const clearDraggedEntry = () => {
    draggedEntryMouseOffset = null
    draggedEntry = null
    mouseStartTime = null
    startX = null
    startY = null
    dragging = false
  }

  const clearNewEntry = () => {
    newEntry = null
    mouseStartTime = null
  }

  // this creates a placeholder for a new schedule entry and make it resizable
  const createNewEntry = (mouse) => {
    newEntry = {
      startTime: roundTimeDown(mouse),
      endTime: roundTimeDown(mouse) + 15
    }
  }

  // helper function to convert Vuetify day & time object into timestamp
  const toTime = (tms) => {
    return new Date(tms.year, tms.month - 1, tms.day, tms.hour, tms.minute).getTime()
  }

  const roundTimeDown = (time) => {
    const roundTo = 15 // minutes
    const roundDownTime = roundTo * 60 * 1000

    return time - time % roundDownTime
  }

  const roundTimeUp = (time) => {
    const roundTo = 15 // minutes
    const roundDownTime = roundTo * 60 * 1000

    return time + (roundDownTime - (time % roundDownTime))
  }

  /**
   * exposed methods
   */

  // triggered with MouseDown event on a calendar entry
  const entryMouseDown = ({ event: entry, timed, nativeEvent }) => {
    // cancel drag & drop if button is not left button
    if (nativeEvent.button !== 0) { return }

    if (editable.value && entry && timed) {
      // start Drag & Drop
      startX = nativeEvent.x
      startY = nativeEvent.y
      draggedEntry = entry
      draggedEntryMouseOffset = null // not know yet: will be populated by timeMouseDown event
    }
  }

  // triggered with MouseDown event anywhere on the calendar (independent of clicking on entry or not)
  const timeMouseDown = (tms, nativeEvent) => {
    // cancel drag & drop if button is not left button
    if (nativeEvent.button !== 0) { return }

    if (editable.value) {
      const mouse = toTime(tms)

      if (mouseStartTime === null) {
        mouseStartTime = mouse
      }

      // Drag hast just started
      if (draggedEntry) {
        const start = draggedEntry.startTime
        draggedEntryMouseOffset = mouse - start
      } else {
      // No entry is being dragged --> create a placeholder for a new schedule entry
        createNewEntry(mouse)
      }
    }
  }

  // triggered when mouse is being moved in calendar (independent whether drag & drop is ongoing or not)
  const timeMouseMove = (tms, nativeEvent) => {
    if (editable.value) {
      const mouse = toTime(tms)

      if (draggedEntry) {
        if (withinThreshold(nativeEvent)) { return }

        // move existing
        dragging = true
        moveDraggedEntry(mouse)
      } else if (resizedEntry) {
        // resize existing
        resizeEntry(resizedEntry, mouse)
      } else if (newEntry) {
        // resize placeholder
        resizeEntry(newEntry, mouse)
        emit('changePlaceholder', newEntry.startTime, newEntry.endTime)
      }
    }
  }

  // triggered with MouseUp Event anywhere in the calendar
  const timeMouseUp = () => {
    if (!editable.value) {
      return
    }

    // Drag & Drop
    if (dragging) {
      update(draggedEntry, draggedEntry.periodOffset, draggedEntry.length)
    } else if (newEntry) {
      // placeholder for new schedule entry was created --> open dialog to create new activity
      emit('newEntry', newEntry.startTime, newEntry.endTime)
    } else if (resizedEntry) {
      if (resizedEntry.endTime !== resizedEntryOldEndTime) {
      // existing entry was resized --> save to API
        update(resizedEntry, resizedEntry.periodOffset, resizedEntry.length)
      }
    }

    clearDraggedEntry()
    clearNewEntry()
    clearResizedEntry()
  }

  // treat mouseleave as a mouseUp event (finish operation and save last known status)
  const nativeMouseLeave = () => {
    timeMouseUp()
  }

  // start resize operation (needs to be called manually from resize handle)
  const startResize = (event) => {
    resizedEntry = event
    mouseStartTime = event.startTime
    resizedEntryOldEndTime = event.endTime
  }

  return {
    listeners: {
      'mousedown:event': entryMouseDown,
      'mousedown:time': timeMouseDown,
      'mousemove:time': timeMouseMove,
      'mouseup:time': timeMouseUp,
      'mouseleave.native': nativeMouseLeave
    },
    startResize
  }
}
