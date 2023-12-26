import dayjs from './dayjs.js'

// converts a timestamp (local timezone) into ISO String format (UTC timezone)
function timestampToUtcString(timestamp) {
  return dayjs.utc(dayjs.tz(timestamp).format('YYYY-MM-DD HH:mm')).format()
}

// converts ISO String format (UTC timezone) into a timestamp (local timezone)
function utcStringToTimestamp(string) {
  return dayjs.tz(dayjs.utc(string).format('YYYY-MM-DD HH:mm')).valueOf()
}

export { timestampToUtcString, utcStringToTimestamp }
