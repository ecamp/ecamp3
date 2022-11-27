import dayjs from './dayjs.js'

function dateShort(dateTimeString, tc) {
  return dayjs.utc(dateTimeString).format(tc('global.datetime.dateShort'))
}

function dateLong(dateTimeString, tc) {
  return dayjs.utc(dateTimeString).format(tc('global.datetime.dateLong'))
}

function hourShort(dateTimeString, tc) {
  return dayjs.utc(dateTimeString).format(tc('global.datetime.hourShort'))
}

function timeDurationShort(start, end, tc) {
  const startTime = dayjs.utc(start)
  const endTime = dayjs.utc(end)
  const duration = dayjs(endTime.diff(startTime))
  const durationInMinutes = duration.valueOf() / 1000 / 60
  if (durationInMinutes < 60) {
    return tc('global.datetime.duration.minutesOnly', 0, {
      minutes: durationInMinutes,
    })
  }
  if (durationInMinutes % 60 === 0) {
    return tc('global.datetime.duration.hoursOnly', 0, {
      hours: durationInMinutes / 60,
    })
  }
  return tc('global.datetime.duration.hoursAndMinutes', 0, {
    hours: Math.floor(durationInMinutes / 60.0),
    minutes: durationInMinutes % 60,
  })
}

// short format of dateTime range
// doesn't repeat end date if on the same day
function rangeShort(start, end, tc) {
  let result = ''

  result += dateShort(start, tc)
  result += ' '
  result += hourShort(start, tc)

  result += ' - '

  if (dateShort(start, tc) !== dateShort(end, tc)) {
    result += dateShort(end, tc)
    result += ' '
  }

  result += hourShort(end, tc)

  return result
}

// format of date range
function dateRange(start, end, tc) {
  if (dateLong(start, tc) === dateLong(end, tc)) {
    return dateLong(start, tc)
  }
  return `${dateShort(start, tc)} - ${dateLong(end, tc)}`
}

export { dateShort, dateLong, timeDurationShort, hourShort, dateRange, rangeShort }
