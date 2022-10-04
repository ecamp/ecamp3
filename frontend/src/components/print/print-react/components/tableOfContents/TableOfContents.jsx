// eslint-disable-next-line no-unused-vars
import React from 'react'
import { Page, Text } from '@react-pdf/renderer'
import styles from '../styles.js'
import Toc from './entry/Toc.jsx'
import Picasso from './entry/Picasso.jsx'
import Program from './entry/Program.jsx'
import Activity from './entry/Activity.jsx'

const entryComponents = {
  Toc,
  Picasso,
  Program,
  Activity,
}

function TableOfContents(props) {
  return (
    <Page size="A4" orientation={'portrait'} style={styles.page}>
      <Text
        id={'entry-' + props.index}
        bookmark={props.$tc('print.toc.title')}
        style={styles.h1}
      >
        {props.$tc('print.toc.title')}
      </Text>
      {props.config.contents.map((entry, index) => {
        if (typeof entryComponents[entry.type] !== 'undefined') {
          return React.createElement(entryComponents[entry.type], {
            ...props,
            entry,
            index,
          })
        }
        return <Text>{entry.type}</Text>
      })}
    </Page>
  )
}

export default TableOfContents
