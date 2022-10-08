import dayjs from '@/common/helpers/dayjs.js'

export default function longestTime(times) {
  return dayjs()
    .hour(0)
    .minute(times[times.length - 1][0] * 60)
    .second(0)
    .format('LT')
}
