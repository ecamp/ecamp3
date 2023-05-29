// eslint-disable-next-line no-unused-vars
import React from 'react'
import PicassoPage from './PicassoPage.jsx'
import { splitDaysIntoPages } from '@/common/helpers/picasso.js'

function Picasso(props) {
  return props.content.options.periods.map((periodUri) => {
    const period = props.store.get(periodUri)
    const maxDaysPerPage = props.content.options.orientation === 'L' ? 8 : 4
    const picassoPages = splitDaysIntoPages(period.days().items, maxDaysPerPage)

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
