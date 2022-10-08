// eslint-disable-next-line no-unused-vars
import React from 'react'
import { Link, Text } from '@react-pdf/renderer'
import tocStyles from '../tocStyles.js'

function Picasso(props) {
  return props.entry.options.periods.map((periodUri) => {
    const period = props.store.get(periodUri)
    return (
      <Link style={tocStyles.entry} href={`#${props.id}-${period.id}`} key={period.id}>
        <Text>{props.$tc('print.picasso.title', { period: period.description })}</Text>
      </Link>
    )
  })
}

export default Picasso
