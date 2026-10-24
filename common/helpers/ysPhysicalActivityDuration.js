import dayjs from './dayjs.js'
import { durationShort } from './dateHelperUTCFormatted.js'

const ysPhysicalActivityDurationOptions = [30, 45, 60, 75, 90, 105, 120]

function ysPhysicalActivityDuration(minutes, tc) {
  if (minutes === null || minutes === undefined) return ''

  return durationShort(dayjs.duration(minutes, 'minutes'), tc)
}

function ysPhysicalActivityDurationFormatted(minutes, tc) {
  if (minutes === null || minutes === undefined) return ''

  return tc('entity.scheduleEntry.fields.ysPhysicalActivityDurationFormatted', {
    duration: ysPhysicalActivityDuration(minutes, tc),
  })
}

export {
  ysPhysicalActivityDurationOptions,
  ysPhysicalActivityDuration,
  ysPhysicalActivityDurationFormatted,
}
