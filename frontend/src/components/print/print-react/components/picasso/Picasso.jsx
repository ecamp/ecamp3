// eslint-disable-next-line no-unused-vars
import React from 'react'
import PicassoPage from './PicassoPage.jsx'
import { calculateBedtime, splitDaysIntoPages } from '@/common/helpers/picasso.js'
import dayjs from '@/common/helpers/dayjs.js'
import sortBy from 'lodash/sortBy.js'

function Picasso(props) {
  return props.content.options.periods.map((periodUri) => {
    const period = props.store.get(periodUri)
    const days = sortBy(period.days().items, (day) => dayjs.utc(day.start).unix())
    const maxDaysPerPage = props.content.options.orientation === 'L' ? 8 : 4
    const picassoPages = splitDaysIntoPages(days, maxDaysPerPage)
    const timeStep = 1
    const { getUpTime, bedtime } = calculateBedtime(
      period.scheduleEntries().items,
      dayjs,
      dayjs.utc(days[0].start),
      dayjs.utc(days[days.length - 1].end),
      timeStep
    )

    return (
      <React.Fragment>
        {picassoPages.map((days) => (
          <PicassoPage
            key={days[0].id}
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
