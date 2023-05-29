/**
 * Splits a set of days into pages, such that all pages contain a similar number of days.
 *
 * @param days {array} set of days to split into pages
 * @param maxDaysPerPage {number} maximum number of days to put on one page
 * @returns {array} list of pages, each containing a list of the days on the page
 */
export function splitDaysIntoPages(days, maxDaysPerPage) {
  const numberOfDays = days.length
  const numberOfPages = Math.ceil(numberOfDays / maxDaysPerPage)
  const daysPerPage = Math.floor(numberOfDays / numberOfPages)
  const numLargerPages = numberOfDays % numberOfPages
  let nextUnassignedDayIndex = 0
  if (isNaN(numberOfPages)) return []

  return [...Array(numberOfPages).keys()].map((i) => {
    const isLargerPage = i < numLargerPages
    const numDaysOnCurrentPage = daysPerPage + (isLargerPage ? 1 : 0)
    const firstDayIndex = nextUnassignedDayIndex
    nextUnassignedDayIndex = firstDayIndex + numDaysOnCurrentPage
    return { // TODO calculate optimal day cutoff hour
      days: days.filter((day, index) => {
        return index >= firstDayIndex && index < nextUnassignedDayIndex
      })
    }
  })
}
