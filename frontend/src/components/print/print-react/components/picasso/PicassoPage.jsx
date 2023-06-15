// eslint-disable-next-line no-unused-vars
import React from 'react'
import { Page, Text, View } from '@react-pdf/renderer'
import styles from '../styles.js'
import picassoStyles from './picassoStyles.js'
import TimeColumn from './TimeColumn.jsx'
import DayColumn from './DayColumn.jsx'
import TimeColumnSpacer from './TimeColumnSpacer.jsx'
import DayHeader from './DayHeader.jsx'
import PicassoFooter from './PicassoFooter.jsx'
import YSLogo from './YSLogo.jsx'
import Categories from './Categories.jsx'

/**
 * Generates an array of time row descriptions, used for labeling the vertical axis of the picasso.
 * Format of each array element: [hour, weight] where weight determines how tall the time row is rendered.
 *
 * @param getUpTime the first time row (the very top of the picasso)
 * @param bedtime the last time row (the very bottom of the picasso)
 * @param timeStep size of the time rows, in hours
 * @returns {*[[hour: number, weight: number]]}
 */
function generateTimes({ getUpTime, bedtime, timeStep }) {
  const times = []
  for (let i = 0; getUpTime + i * timeStep < bedtime; i++) {
    // TODO The weight could also be generated depending on the schedule entries present in the camp:
    //   e.g. give less weight to hours that contain no schedule entries.
    const weight = 1
    times.push([getUpTime + i * timeStep, weight])
  }
  // this last hour is only needed for defining the length of the day. The weight should be 0.
  times.push([bedtime, 0])
  return times
}

function PicassoPage(props) {
  const period = props.period
  const days = props.days
  const orientation = props.content.options.orientation
  const anyDayResponsibles = days.some((day) => day.dayResponsibles().items.length > 0)
  const scheduleEntries = period.scheduleEntries().items
  const times = generateTimes({
    getUpTime: props.getUpTime,
    bedtime: props.bedtime,
    timeStep: props.timeStep,
  })

  return (
    <Page
      size="A4"
      orientation={orientation === 'L' ? 'landscape' : 'portrait'}
      style={styles.page}
      key={period._meta.self}
    >
      <View style={picassoStyles.titleContainer}>
        <Text
          id={`${props.id}-${period.id}`}
          bookmark={{
            title: props.$tc('print.picasso.title', { period: period.description }),
            fit: true,
          }}
          style={{ ...styles.h1, ...picassoStyles.title }}
        >
          {props.$tc('print.picasso.title', { period: period.description })}
        </Text>
        <Text>{period.camp().organizer}</Text>
        {period.camp().printYSLogoOnPicasso ? (
          <YSLogo
            size={picassoStyles.ysLogo.size}
            styles={picassoStyles.ysLogo}
            locale={props.locale}
          />
        ) : (
          <React.Fragment />
        )}
      </View>
      <View style={{ ...picassoStyles.calendarContainer, border: '1pt solid white' }}>
        <TimeColumnSpacer times={times.slice(0, times.length - 1)} />
        {days.map((day) => (
          <DayHeader
            day={day}
            showDayResponsibles={anyDayResponsibles}
            key={day.id}
            styles={{
              borderLeft: day.id === days[0].id ? '1pt solid white' : '',
              borderRight: '1pt solid white',
            }}
            {...props}
          />
        ))}
        <TimeColumnSpacer times={times.slice(0, times.length - 1)} />
      </View>
      <View style={picassoStyles.calendarContainer}>
        <TimeColumn times={times.slice(0, times.length - 1)} align={'right'} />
        {days.map((day) => {
          return (
            <DayColumn
              key={day.id}
              styles={{
                borderLeft: day.id === days[0].id ? '1pt solid grey' : '',
                borderRight: '1pt solid grey',
              }}
              times={times}
              day={day}
              scheduleEntries={scheduleEntries}
            />
          )
        })}
        <TimeColumn times={times.slice(0, times.length - 1)} align={'left'} />
      </View>
      <Categories period={period} />
      <PicassoFooter period={period} {...props} />
    </Page>
  )
}

export default PicassoPage
