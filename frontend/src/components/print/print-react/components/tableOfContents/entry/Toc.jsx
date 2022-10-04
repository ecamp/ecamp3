// eslint-disable-next-line no-unused-vars
import React from 'react'
import { Link, Text } from '@react-pdf/renderer'
import tocStyles from '../tocStyles.js'

function Toc(props) {
  return (
    <Link style={tocStyles.entry} href={'#entry-' + props.index}>
      <Text>{props.$tc('print.toc.title')}</Text>
    </Link>
  )
}

export default Toc
