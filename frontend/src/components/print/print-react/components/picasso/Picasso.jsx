// eslint-disable-next-line no-unused-vars
import React from 'react'
import dayjs from '@/common/helpers/dayjs.js'
import PicassoPage from './PicassoPage.jsx'

function Picasso(props) {
  return props.content.options.periods.map((periodUri) => {
    const period = props.store.get(periodUri)
    const maxDaysPerPage = props.content.options.orientation == 'L' ? 8 : 4
    const start = dayjs.utc(period.start)
    const end = dayjs.utc(period.end)
    const hours = end.diff(start, 'hours')
    const days = Math.floor(hours / 24) + 1
    const numberOfPages = Math.ceil(days / maxDaysPerPage)
    const daysPerPage = Math.floor(days / numberOfPages)
    const numLargerPages = days % numberOfPages
    let nextUnassignedDayIndex = 0
    const picassoPages = [...Array(numberOfPages).keys()].map((i) => {
      const isLargerPage = i < numLargerPages
      const numDaysOnCurrentPage = daysPerPage + (isLargerPage ? 1 : 0)
      const firstDayIndex = nextUnassignedDayIndex
      nextUnassignedDayIndex = firstDayIndex + numDaysOnCurrentPage
      const pageStartDate = dayjs
        .utc(period.start)
        .add(firstDayIndex, 'days') // The first n pages each have one day more
        .hour(0) // TODO set correct hour depending on auto-calculated optimal day cutoff hour
        .minute(0)
        .second(0)
      const pageEndDate = dayjs
        .utc(period.start)
        .add(nextUnassignedDayIndex, 'days')
        .hour(0) // TODO set correct hour depending on auto-calculated optimal day cutoff hour
        .minute(0)
        .second(0)
      return {
        days: period.days().items.filter((day) => {
          return day.dayOffset >= firstDayIndex && day.dayOffset < nextUnassignedDayIndex
        }),
        start: pageStartDate,
        end: pageEndDate,
      }
    })

    return (
      <React.Fragment>
        {picassoPages.map((page) => (
          <PicassoPage period={period} {...page} {...props} />
        ))}
      </React.Fragment>
    )
  })
}

export default Picasso
