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

function hourLong(dateTimeString, tc) {
  return dayjs.utc(dateTimeString).format(tc('global.datetime.hourLong'))
}

function timeDurationShort(start, end, tc) {
  const startTime = dayjs.utc(start)
  const endTime = dayjs.utc(end)
  const duration = dayjs.duration(endTime.diff(startTime))

  return tc(
    duration.asDays() >= 1
        ? duration.hours() === 0
          ? duration.minutes() === 0
            ? 'global.datetime.duration.daysOnly'
            : 'global.datetime.duration.daysAndMinutes'
          : duration.minutes() === 0
            ? 'global.datetime.duration.daysAndHours'
            : 'global.datetime.duration.daysAndHoursAndMinutes'
        : duration.asMinutes() < 60
          ? 'global.datetime.duration.minutesOnly'
          : duration.minutes() === 0
            ? 'global.datetime.duration.hoursOnly'
            : 'global.datetime.duration.hoursAndMinutes',
    0,
    {
      days: Math.floor(duration.asDays()),
      hours: duration.hours(),
      minutes: duration.minutes(),
    }
  )
}

// short format of dateTime range
// doesn't show any date
function rangeTime(start, end, tc) {
  return hourShort(start, tc) + ' - ' + hourShort(end, tc)
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

// long end part of dateTime range format
// doesn't repeat end date if on the same day
function rangeLongEnd(start, end, tc) {
  let result = ''

  result += hourLong(start, tc)

  result += ' - '

  if (dateShort(start, tc) !== dateShort(end, tc)) {
    result += dateShort(end, tc)
    result += ' '
  }

  result += hourLong(end, tc)

  return result
}

// format of date range
function dateRange(start, end, tc) {
  if (dateLong(start, tc) === dateLong(end, tc)) {
    return dateLong(start, tc)
  }
  return `${dateShort(start, tc)} - ${dateLong(end, tc)}`
}

export {
  dateShort,
  dateLong,
  timeDurationShort,
  hourShort,
  hourLong,
  dateRange,
  rangeTime,
  rangeShort,
  rangeLongEnd,
}
