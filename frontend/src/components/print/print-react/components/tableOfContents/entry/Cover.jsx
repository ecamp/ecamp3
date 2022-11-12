// eslint-disable-next-line no-unused-vars
import React from 'react'
import { Link, Text } from '@react-pdf/renderer'
import tocStyles from '../tocStyles.js'

function Cover(props) {
  return (
    <Link style={tocStyles.entry} href={`#${props.id}`}>
      <Text>{props.$tc('print.cover.title')}</Text>
    </Link>
  )
}

export default Cover
