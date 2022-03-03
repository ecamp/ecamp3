import dayjs from './dayjs.js'

// converts a timestamp (local timezone) into ISO String format (UTC timezone)
function timestampToUtcString (timestamp) {
  return dayjs.utc(dayjs(timestamp).format('YYYY-MM-DD HH:mm')).format()
}

// converts ISO String format (UTC timezone) into a timestamp (local timezone)
function utcStringToTimestamp (string) {
  return dayjs(dayjs.utc(string).format('YYYY-MM-DD HH:mm')).valueOf()
}

export {
  timestampToUtcString,
  utcStringToTimestamp,
}
