import { ref } from '@vue/composition-api'
import { apiStore as api } from '@/plugins/store'
import { i18n } from '@/plugins'

import { defineHelpers } from '@/common/helpers/scheduleEntry/dateHelperLocal.js'

export default function useDragAndDrop (editable, period, dialogActivityCreate, showScheduleEntry) {
  /**
   * internal data (not exposed)
   */

  // timestamp of mouse location when drag & drop event started
  const mouseStartTime = ref(null)

  // reference to dragged scheduleEntry (null = no drag & drop ongoing)
  const draggedEntry = ref(null)

  // offset between (dragged) entry startTime and mouse location
  const draggedEntryMouseOffset = ref(null)

  // existing entry that is being resized
  const resizedEntry = ref(null)

  // original end time of entry which is being resized
  const resizedEntryOldEndTime = ref(null)

  // used to detect mouse down events that were interpreted as clicks
  const openedInNewTab = ref(false)

  /**
   * external data
   */

  // true if API patch is being performed
  const isSaving = ref(false)

  // contains error message if API patch was unsuccessful
  const patchError = ref(null)

  // temporary placeholder for new schedule entry, when created via drag & drop
  const newEntry = ref(null)

  /**
   * internal methods
   */

  // move an existing entry
  const moveDraggedEntry = (mouse) => {
    const start = draggedEntry.value.startTime
    const end = draggedEntry.value.endTime
    const duration = end - start

    const newStart = roundTimeDown((mouse - draggedEntryMouseOffset.value))
    const newEnd = newStart + duration

    draggedEntry.value.startTime = newStart
    draggedEntry.value.endTime = newEnd
  }

  // resize an entry (existing or placeholder)
  const resizeExistingEntry = (mouse) => {
    const mouseRounded = roundTimeUp(mouse)
    const min = Math.min(mouseRounded, roundTimeDown(mouseStartTime.value))
    const max = Math.max(mouseRounded, roundTimeDown(mouseStartTime.value))

    resizedEntry.value.startTime = min
    resizedEntry.value.endTime = max
  }

  // resize the placeholder for new entry
  const resizeNewEntry = (mouse) => {
    const mouseRounded = roundTimeUp(mouse)
    const min = Math.min(mouseRounded, roundTimeDown(mouseStartTime.value))
    const max = Math.max(mouseRounded, roundTimeDown(mouseStartTime.value))

    newEntry.value.startTime = min
    newEntry.value.endTime = max
  }

  const clearResizedEntry = () => {
    resizedEntry.value = null
    resizedEntryOldEndTime.value = null
    mouseStartTime.value = null
  }

  const clearDraggedEntry = () => {
    draggedEntryMouseOffset.value = null
    draggedEntry.value = null
    mouseStartTime.value = null
  }

  const clearNewEntry = () => {
    newEntry.value = null
    mouseStartTime.value = null
  }

  // this creates a placeholder for a new schedule entry and make it resizable
  const createNewEntry = (mouse) => {
    newEntry.value = defineHelpers({
      number: null,
      period: () => period.value,
      periodOffset: 0,
      length: 0,
      activity: () => ({
        title: i18n.tc('entity.activity.new'),
        location: '',
        camp: period.value.camp,
        category: () => ({
          id: null,
          short: null,
          color: 'grey elevation-4 v-event--temporary'
        })
      }),
      tmpEvent: true
    }, true)
    newEntry.value.startTime = roundTimeDown(mouse)
    newEntry.value.endTime = newEntry.value.startTime + 15
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
    if (!entry.tmpEvent && (nativeEvent.button === 1 || nativeEvent.metaKey || nativeEvent.ctrlKey)) {
      // Click with middle mouse button, or click while holding cmd/ctrl opens new tab
      showScheduleEntry(entry, true)
      openedInNewTab.value = true
    } else if (nativeEvent.button === 2) {
      // don't move event if middle mouse button
    } else if (editable.value) {
      if (entry && timed) {
        // start Drag & Drop
        draggedEntry.value = entry
        draggedEntryMouseOffset.value = null // not know yet: will be populated by timeMouseDown event
      }
    }
  }

  // triggered with MouseDown event anywhere on the calendar (independent of clicking on entry or not)
  const timeMouseDown = (tms) => {
    // if entryMouseDown was interpreted as click event --> cancel drag & drop and return early
    if (openedInNewTab.value) {
      openedInNewTab.value = false
      return
    }

    if (editable.value) {
      const mouse = toTime(tms)

      if (mouseStartTime.value === null) {
        mouseStartTime.value = mouse
      }

      // Drag hast just started
      if (draggedEntry.value) {
        const start = draggedEntry.value.startTime
        draggedEntryMouseOffset.value = mouse - start
      } else {
      // No entry is being dragged --> create a placeholder for a new schedule entry
        createNewEntry(mouse)
      }
    }
  }

  // triggered when mouse is being moved in calendar (independent whether drag & drop is ongoing or not)
  const timeMouseMove = (tms) => {
    if (editable.value) {
      const mouse = toTime(tms)

      if (draggedEntry.value) {
        moveDraggedEntry(mouse)
      } else if (resizedEntry.value) {
        resizeExistingEntry(mouse)
      } else if (newEntry.value) {
        resizeNewEntry(mouse)
      }
    }
  }

  // triggered with MouseUp Event anywhere in the calendar
  const timeMouseUp = (tms) => {
    if (!editable.value) {
      return
    }

    // Drag & Drop
    if (draggedEntry.value) {
      const minuteThreshold = 15
      const threshold = minuteThreshold * 60 * 1000
      const now = toTime(tms)

      // interpret shifts below 15min as a click event
      if (Math.abs(now - mouseStartTime.value) < threshold) {
        showScheduleEntry(draggedEntry.value)
        return
      }

      // save to API
      const patchedScheduleEntry = {
        periodOffset: draggedEntry.value.periodOffset,
        length: draggedEntry.value.length
      }
      isSaving.value = true
      api.patch(draggedEntry.value._meta.self, patchedScheduleEntry).then(() => {
        patchError.value = null
        isSaving.value = false
      }).catch((error) => {
        patchError.value = error
      })

      // reset draggedEntry
      clearDraggedEntry()
    } else if (newEntry.value) {
      // placeholder for new schedule entry was created --> open dialog to create new activity
      dialogActivityCreate(newEntry.value, clearNewEntry)
    } else if (resizedEntry.value) {
      if (resizedEntry.value.endTime !== resizedEntryOldEndTime.value) {
      // existing entry was resized --> save to API
        const patchedScheduleEntry = {
          periodOffset: resizedEntry.value.periodOffset,
          length: resizedEntry.value.length
        }
        isSaving.value = true
        api.patch(resizedEntry.value._meta.self, patchedScheduleEntry).then(() => {
          patchError.value = null
          isSaving.value = false
        }).catch((error) => {
          patchError.value = error
        })
      }

      clearResizedEntry()
    }
  }

  // TODO docu: for which use case is this needed??
  const nativeMouseUp = () => {
    if (editable.value) {
      if (resizedEntry.value) {
        if (resizedEntryOldEndTime.value) {
          resizedEntry.value.endTime = resizedEntryOldEndTime.value
        }
      }
      clearDraggedEntry()
      clearResizedEntry()
    }
  }

  const extendBottom = (event) => {
    resizedEntry.value = event
    mouseStartTime.value = event.startTime
    resizedEntryOldEndTime.value = event.endTime
  }

  return {
    entryMouseDown,
    timeMouseDown,
    timeMouseMove,
    timeMouseUp,
    nativeMouseUp,
    extendBottom,
    isSaving,
    patchError,
    newEntry
  }
}
