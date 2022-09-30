// eslint-disable-next-line no-unused-vars
import React from 'react'
import { View, Text } from '@react-pdf/renderer'
import dayjs from '@/common/helpers/dayjs.js'
import picassoStyles from './picassoStyles.js'

function renderDate(day) {
  return dayjs.utc(day.start).hour(0).minute(0).second(0).format('ddd LL')
}

function DayHeader({ day, styles }) {
  return (
    <View style={{ ...picassoStyles.dayHeader, ...styles }}>
      <Text style={picassoStyles.dayHeaderText}>{renderDate(day)}</Text>
    </View>
  )
}

export default DayHeader
