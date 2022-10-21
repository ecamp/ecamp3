// eslint-disable-next-line no-unused-vars
import React from 'react'
import { Page } from '@react-pdf/renderer'
import styles from '../styles.js'
import StoryPeriod from './StoryPeriod.jsx'

function Story(props) {
  return (
    <Page key={props.id} style={styles.page} id={props.id}>
      {props.content.options.periods.map((periodUri) => (
        <StoryPeriod key={periodUri} periodUri={periodUri} {...props} />
      ))}
    </Page>
  )
}

export default Story
