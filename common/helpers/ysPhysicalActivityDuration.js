const ysPhysicalActivityDurationOptions = [30, 45, 60, 75, 90, 105, 120]

function ysPhysicalActivityDurationLong(minutes, tc) {
  if (minutes === null || minutes === undefined) return ''

  const hours = Math.floor(minutes / 60)
  const remainingMinutes = minutes % 60
  return tc(
    hours === 0
      ? 'global.datetime.duration.minutesOnly'
      : remainingMinutes === 0
        ? 'global.datetime.duration.hoursOnly'
        : 'global.datetime.duration.hoursAndMinutes',
    { hours, minutes: remainingMinutes }
  )
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
