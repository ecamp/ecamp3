import dayjs from './dayjs.js'
import { durationShort } from './dateHelperUTCFormatted.js'

const ysPhysicalActivityDurationOptions = [30, 45, 60, 75, 90, 105, 120]

function ysPhysicalActivityDurationLong(minutes, tc) {
  if (minutes === null || minutes === undefined) return ''

  return durationShort(dayjs.duration(minutes, 'minutes'), tc)
}

function ysPhysicalActivityDurationShort(minutes, tc) {
  if (minutes === null || minutes === undefined) return ''

  return tc('entity.scheduleEntry.fields.ysPhysicalActivityDurationShort', { minutes })
}

export {
  ysPhysicalActivityDurationOptions,
  ysPhysicalActivityDurationLong,
  ysPhysicalActivityDurationShort,
}
