import maxBy from 'lodash-es/maxBy.js'

export function timeColumnWidth(times, dayjs) {
  // we could do better than this heuristic...
  return Math.ceil(5.3 * longestTime(times, dayjs).length) + 4
}

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
