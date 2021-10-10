// eslint-disable-next-line no-unused-vars
import React from 'react'
import pdf from '@react-pdf/renderer'
import styles from '../styles.js'
import TimeColumn from './TimeColumn.jsx'
import DayColumn from './DayColumn.jsx'

const { Page, View, Text } = pdf

function Picasso ({ camp, period }) {
  const pageStyles = {
    padding: 40,
    fontSize: 12,
    display: 'flex',
    flexDirection: 'column'
  }
  const columnWrapperStyles = {
    flexGrow: '1',
    // must use height: 0 together with vertical flexGrow, because otherwise the height will be
    // set based on the children, which we don't want
    height: 0,
    display: 'flex',
    flexDirection: 'row',
    alignItems: 'stretch',
    border: '1px solid black'
  }

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
    [23, 1]
  ]

  return <React.Fragment>
    <Page size="A4" orientation={period.days().items.length > 1 ? 'landscape' : 'portrait'} style={ pageStyles }>
      <Text id="picasso" style={styles.h1}>Grobprogramm {period.description}</Text>
      <View style={ columnWrapperStyles }>
        <TimeColumn times={times} />
        {period.days().items.map(day => {
          return <DayColumn key={day.id}
                            styles={{ borderLeft: (day.id === period.days().items[0].id) ? '1px solid grey' : '', borderRight: '1px solid grey' }}
                            times={times}
                            day={day}
                            scheduleEntries={period.scheduleEntries().items}/>
        })}
        <TimeColumn times={times} />
      </View>
    </Page>
  </React.Fragment>
}

export default Picasso
