import dayjs from './dayjs.js'
import { durationShort } from './dateHelperUTCFormatted.js'

const ysPhysicalActivityDurationOptions = [30, 45, 60, 75, 90, 105, 120]

function isYsPhysicalActivityDurationOpenEnded(minutes) {
  return minutes >= ysPhysicalActivityDurationOptions.at(-1)
}

function ysPhysicalActivityDuration(minutes, tc) {
  if (minutes === null || minutes === undefined) return ''

  const duration = durationShort(dayjs.duration(minutes, 'minutes'), tc)
  if (!isYsPhysicalActivityDurationOpenEnded(minutes)) return duration

  return tc('entity.scheduleEntry.fields.ysPhysicalActivityDurationOrMore', { duration })
}

function ysPhysicalActivityDurationShort(minutes, tc) {
  const duration = dayjs.duration(minutes, 'minutes')
  if (!isYsPhysicalActivityDurationOpenEnded(minutes)) return durationShort(duration, tc)

  return tc('entity.scheduleEntry.fields.ysPhysicalActivityDurationOrMoreShort', {
    hours: duration.asHours(),
  })
}

function ysPhysicalActivityDurationFormatted(minutes, tc) {
  if (minutes === null || minutes === undefined) return ''

  return tc('entity.scheduleEntry.fields.ysPhysicalActivityDurationFormatted', {
    duration: ysPhysicalActivityDurationShort(minutes, tc),
  })
}

export {
  ysPhysicalActivityDurationOptions,
  isYsPhysicalActivityDurationOpenEnded,
  ysPhysicalActivityDuration,
  ysPhysicalActivityDurationFormatted,
}
