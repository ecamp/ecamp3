import { hourShort } from '@/common/helpers/dateHelperUTCFormatted.js'

export default function shortScheduleEntryDescription(scheduleEntry, tc) {
  if (!scheduleEntry || scheduleEntry._meta.loading) return ''
  return (
    scheduleEntry?.number ||
    // For numbering style "none", display the activity title and day number instead
    tc('global.shortScheduleEntryDescription', 1, {
      dayNumber: scheduleEntry.dayNumber,
      startTime: hourShort(scheduleEntry.start, tc),
    })
  )
}
