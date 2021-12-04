import { ref } from '@vue/composition-api'
import { apiStore as api } from '@/plugins/store'
import { i18n } from '@/plugins'

import { defineHelpers } from '@/common/helpers/scheduleEntry/dateHelperLocal.js'

export default function useDragAndDrop (editable, period, dialogActivityCreate, showScheduleEntry) {
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

  // used to detect mouse down events that were interpreted as clicks
  let openedInNewTab = false

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
    const start = draggedEntry.startTime
    const end = draggedEntry.endTime
    const duration = end - start

    const newStart = roundTimeDown((mouse - draggedEntryMouseOffset))
    const newEnd = newStart + duration

    draggedEntry.startTime = newStart
    draggedEntry.endTime = newEnd
  }

  // resize an entry (existing or placeholder)
  const resizeExistingEntry = (mouse) => {
    const mouseRounded = roundTimeUp(mouse)
    const min = Math.min(mouseRounded, roundTimeDown(mouseStartTime))
    const max = Math.max(mouseRounded, roundTimeDown(mouseStartTime))

    resizedEntry.startTime = min
    resizedEntry.endTime = max
  }

  // resize the placeholder for new entry
  const resizeNewEntry = (mouse) => {
    const mouseRounded = roundTimeUp(mouse)
    const min = Math.min(mouseRounded, roundTimeDown(mouseStartTime))
    const max = Math.max(mouseRounded, roundTimeDown(mouseStartTime))

    newEntry.value.startTime = min
    newEntry.value.endTime = max
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
  }

  const clearNewEntry = () => {
    newEntry.value = null
    mouseStartTime = null
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
      openedInNewTab = true
    } else if (nativeEvent.button === 2) {
      // don't move event if middle mouse button
    } else if (editable.value) {
      if (entry && timed) {
        // start Drag & Drop
        draggedEntry = entry
        draggedEntryMouseOffset = null // not know yet: will be populated by timeMouseDown event
      }
    }
  }

  // triggered with MouseDown event anywhere on the calendar (independent of clicking on entry or not)
  const timeMouseDown = (tms) => {
    // if entryMouseDown was interpreted as click event --> cancel drag & drop and return early
    if (openedInNewTab) {
      openedInNewTab = false
      return
    }

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
  const timeMouseMove = (tms) => {
    if (editable.value) {
      const mouse = toTime(tms)

      if (draggedEntry) {
        moveDraggedEntry(mouse)
      } else if (resizedEntry) {
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
    if (draggedEntry) {
      const minuteThreshold = 15
      const threshold = minuteThreshold * 60 * 1000
      const now = toTime(tms)

      // interpret shifts below 15min as a click event
      if (Math.abs(now - mouseStartTime) < threshold) {
        showScheduleEntry(draggedEntry)
        return
      }

      // save to API
      const patchedScheduleEntry = {
        periodOffset: draggedEntry.periodOffset,
        length: draggedEntry.length
      }
      isSaving.value = true
      api.patch(draggedEntry._meta.self, patchedScheduleEntry).then(() => {
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
    } else if (resizedEntry) {
      if (resizedEntry.endTime !== resizedEntryOldEndTime) {
      // existing entry was resized --> save to API
        const patchedScheduleEntry = {
          periodOffset: resizedEntry.periodOffset,
          length: resizedEntry.length
        }
        isSaving.value = true
        api.patch(resizedEntry._meta.self, patchedScheduleEntry).then(() => {
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
      if (resizedEntry) {
        if (resizedEntryOldEndTime) {
          resizedEntry.endTime = resizedEntryOldEndTime
        }
      }
      clearDraggedEntry()
      clearResizedEntry()
    }
  }

  const extendBottom = (event) => {
    resizedEntry = event
    mouseStartTime = event.startTime
    resizedEntryOldEndTime = event.endTime
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
