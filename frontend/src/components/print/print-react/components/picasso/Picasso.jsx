// eslint-disable-next-line no-unused-vars
import React from 'react'
import { Page, View, Text } from '@react-pdf/renderer'
import styles from '../styles.js'
import picassoStyles from './picassoStyles.js'
import TimeColumn from './TimeColumn.jsx'
import DayColumn from './DayColumn.jsx'
import TimeColumnSpacer from './TimeColumnSpacer.jsx'
import DayHeader from './DayHeader.jsx'

function Picasso({ period, orientation, $tc }) {
  // Format: [hour, weight] where weight determines how tall the hour is rendered.
  // This could also be generated depending on the schedule entries present in the camp:
  // e.g. give less weight to hours that contain no schedule entries, or detect which hour is best
  // for the "midnight" cutoff (so far everything also works with hours smaller than 0 or greater
  // than 23)
  const times = [
    [0, 1],
    [1, 1],
    [2, 1],
    [3, 1],
    [4, 1],
    [5, 1],
    [6, 1],
    [7, 2],
    [8, 2],
    [9, 2],
    [10, 2],
    [11, 2],
    [12, 2],
    [13, 2],
    [14, 2],
    [15, 2],
    [16, 2],
    [17, 2],
    [18, 2],
    [19, 2],
    [20, 2],
    [21, 2],
    [22, 2],
    [23, 1],
    [24, 0], // this last hour is only needed for defining the length of the day, the weight should be 0
  ]

  const anyDayResponsibles = period
    .days()
    .items.some((day) => day.dayResponsibles().items.length > 0)

  return (
    <Page
      size="A4"
      orientation={orientation === 'L' ? 'landscape' : 'portrait'}
      style={styles.page}
    >
      <Text id="picasso" style={styles.h1}>
        {$tc('print.picasso.title', { period: period.description })}
      </Text>
      <View style={{ ...picassoStyles.calendarContainer, border: '1pt solid white' }}>
        <TimeColumnSpacer times={times.slice(0, times.length - 1)} />
        {period.days().items.map((day) => (
          <DayHeader
            day={day}
            showDayResponsibles={anyDayResponsibles}
            $tc={$tc}
            key={day.id}
            styles={{
              borderLeft: day.id === period.days().items[0].id ? '1pt solid white' : '',
              borderRight: '1pt solid white',
            }}
          />
        ))}
        <TimeColumnSpacer times={times.slice(0, times.length - 1)} />
      </View>
      <View style={picassoStyles.calendarContainer}>
        <TimeColumn
          times={times.slice(0, times.length - 1)}
          align={'right'}
        />
        {period.days().items.map((day) => {
          return (
            <DayColumn
              key={day.id}
              styles={{
                borderLeft: day.id === period.days().items[0].id ? '1pt solid grey' : '',
                borderRight: '1pt solid grey',
              }}
              times={times}
              day={day}
              scheduleEntries={period.scheduleEntries().items}
            />
          )
        })}
        <TimeColumn
          times={times.slice(0, times.length - 1)}
          align={'left'}
        />
      </View>
    </Page>
  )
}

export default Picasso
