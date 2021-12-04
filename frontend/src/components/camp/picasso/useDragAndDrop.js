import { ref } from '@vue/composition-api'
import { apiStore as api } from '@/plugins/store'
import { i18n } from '@/plugins'

import { defineHelpers } from '@/common/helpers/scheduleEntry/dateHelperLocal.js'

export default function useDragAndDrop (editable, period, dialogActivityCreate) {
  // internal data
  const draggedEntry = ref(null)
  const currentEntry = ref(null)
  const mouseStartTime = ref(null)
  const draggedStartTime = ref(null)
  const currentStartTime = ref(null)
  const extendOriginal = ref(null)
  const openedInNewTab = ref(false)

  // external data
  const isSaving = ref(false)
  const patchError = ref(null)
  const tempScheduleEntry = ref({})

  const moveEntryTime = (mouse) => {
    const start = draggedEntry.value.startTime
    const end = draggedEntry.value.endTime
    const duration = end - start
    const newStartTime = mouse - draggedStartTime.value
    const newStart = roundTimeDown(newStartTime)
    const newEnd = newStart + duration

    draggedEntry.value.startTime = newStart
    draggedEntry.value.endTime = newEnd
  }

  const changeEntryTime = (mouse) => {
    const mouseRounded = roundTimeUp(mouse)
    const min = Math.min(mouseRounded, currentStartTime.value)
    const max = Math.max(mouseRounded, currentStartTime.value)

    currentEntry.value.startTime = min
    currentEntry.value.endTime = max
  }

  const clearCurrentEntry = () => {
    currentEntry.value = null
    currentStartTime.value = null
    extendOriginal.value = null
  }

  const clearDraggedEntry = () => {
    draggedStartTime.value = null
    draggedEntry.value = null
  }

  /*
  const clearEntry = () => {
    clearCurrentEntry()
    clearDraggedEntry()
  } */

  const createNewEntry = (mouse) => {
    currentStartTime.value = roundTimeDown(mouse)
    currentEntry.value = defineHelpers({
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
    currentEntry.value.startTime = currentStartTime.value
    currentEntry.value.endTime = currentStartTime.value
    tempScheduleEntry.value = currentEntry.value
  }

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

  const entryMouseDown = ({ event: entry, timed, nativeEvent }) => {
    if (!entry.tmpEvent && (nativeEvent.button === 1 || nativeEvent.metaKey || nativeEvent.ctrlKey)) {
      // Click with middle mouse button, or click while holding cmd/ctrl opens new tab
      // this.showScheduleEntryInNewTab(entry) // TODO: fix
      openedInNewTab.value = true
    } else if (nativeEvent.button === 2) {
      // don't move event if middle mouse button
    } else if (editable.value) {
      if (entry && timed) {
        draggedEntry.value = entry
        draggedStartTime.value = null
        extendOriginal.value = null
      }
    }
  }

  const timeMouseDown = (tms) => {
    if (editable.value) {
      const mouse = toTime(tms)
      if (openedInNewTab.value) {
        openedInNewTab.value = false
        return
      }

      if (mouseStartTime.value === null) {
        mouseStartTime.value = mouse
      }

      if (draggedEntry.value && draggedStartTime.value === null) {
        const start = draggedEntry.value.startTime
        draggedStartTime.value = mouse - start
      } else {
        createNewEntry(mouse)
      }
    }
  }

  const timeMouseMove = (tms) => {
    if (editable.value) {
      const mouse = toTime(tms)

      if (draggedEntry.value && draggedStartTime.value !== null) {
        moveEntryTime(mouse)
      } else if (currentEntry.value && currentStartTime.value !== null) {
        changeEntryTime(mouse)
      }
    }
  }

  const timeMouseUp = (tms) => {
    if (editable.value) {
      if (draggedEntry.value && draggedStartTime.value !== null) {
        const minuteThreshold = 15
        const threshold = minuteThreshold * 60 * 1000
        const now = toTime(tms)
        if (Math.abs(now - mouseStartTime.value) < threshold) {
          // this.showScheduleEntry(draggedEntry.value) // TODO: fix
        } else if (!draggedEntry.value.tmpEvent) {
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
        }
        clearDraggedEntry()
      } else if (currentEntry.value && currentStartTime.value !== null) {
        if (currentEntry.value.tmpEvent) {
          if (!extendOriginal.value) {
            dialogActivityCreate(currentEntry.value, () => { tempScheduleEntry.value = null })
          }
        } else if (currentEntry.value.endTime !== extendOriginal.value) {
          const patchedScheduleEntry = {
            periodOffset: currentEntry.value.periodOffset,
            length: currentEntry.value.length
          }
          isSaving.value = true
          api.patch(currentEntry.value._meta.self, patchedScheduleEntry).then(() => {
            patchError.value = null
            isSaving.value = false
          }).catch((error) => {
            patchError.value = error
          })
        }
        clearCurrentEntry()
      }
      mouseStartTime.value = null
    }
  }

  const nativeMouseUp = () => {
    if (editable.value) {
      if (currentEntry.value) {
        if (extendOriginal.value) {
          currentEntry.value.endTime = extendOriginal.value
        }
      }
      clearDraggedEntry()
      clearCurrentEntry()
    }
  }

  const extendBottom = (event) => {
    currentEntry.value = event
    currentStartTime.value = event.startTime
    extendOriginal.value = event.endTime
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
    tempScheduleEntry
  }
}
