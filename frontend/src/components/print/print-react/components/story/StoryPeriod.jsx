// eslint-disable-next-line no-unused-vars
import React from 'react'
import { Text } from '@react-pdf/renderer'
import styles from '../styles.js'
import StoryDay from './StoryDay.jsx'

function StoryPeriod(props) {
  const period = props.store.get(props.periodUri)
  return (
    <React.Fragment>
      <Text
        id={`${props.id}-${period.id}`}
        bookmark={{
          title:
            props.$tc('components.story.storyPeriod.title') + ': ' + period.description,
          fit: true,
        }}
        style={styles.h1}
      >
        {props.$tc('components.story.storyPeriod.title')}: {period.description}
      </Text>
      {period.days().items.map((day) => (
        <StoryDay key={day.id} day={day} period={period} {...props} />
      ))}
    </React.Fragment>
  )
}

export default StoryPeriod
