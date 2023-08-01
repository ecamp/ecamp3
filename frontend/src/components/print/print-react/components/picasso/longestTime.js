import dayjs from '@/common/helpers/dayjs.js'
import { maxBy } from 'lodash'

export default function longestTime(times) {
  return dayjs()
    .hour(0)
    .minute(findLongestText(times)[0] * 60)
    .second(0)
    .format('LT')
}

function findLongestText(times) {
  return maxBy(times, (time) => ((time[0] - 1) % 24) + 1)
}
