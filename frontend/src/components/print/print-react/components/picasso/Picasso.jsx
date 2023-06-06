// eslint-disable-next-line no-unused-vars
import React from 'react'
import PicassoPage from './PicassoPage.jsx'
import { calculateBedtime, splitDaysIntoPages } from '@/common/helpers/picasso.js'
import dayjs from '@/common/helpers/dayjs.js'

function Picasso(props) {
  return props.content.options.periods.map((periodUri) => {
    const period = props.store.get(periodUri)
    const maxDaysPerPage = props.content.options.orientation === 'L' ? 8 : 4
    const picassoPages = splitDaysIntoPages(period.days().items, maxDaysPerPage)
    const timeStep = 1
    const { getUpTime, bedtime } = calculateBedtime(
      period.scheduleEntries().items,
      dayjs,
      period.days().items[0].start,
      period.days().items[period.days().items.length - 1].start,
      timeStep
    )

    return (
      <React.Fragment>
        {picassoPages.map((days) => (
          <PicassoPage
            period={period}
            days={days}
            bedtime={bedtime}
            getUpTime={getUpTime}
            timeStep={timeStep}
            {...props}
          />
        ))}
      </React.Fragment>
    )
  })
}

export default Picasso
