import maxBy from 'lodash/maxBy.js'

export default function longestTime(times, dayjs) {
  return dayjs()
    .hour(0)
    .minute(findLongestText(times)[0] * 60)
    .second(0)
    .format('LT')
}

function findLongestText(times) {
  return maxBy(times, (time) => ((time[0] - 1) % 24) + 1)
}
