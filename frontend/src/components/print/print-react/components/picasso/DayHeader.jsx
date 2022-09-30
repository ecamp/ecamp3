// eslint-disable-next-line no-unused-vars
import React from 'react'
import { View, Text } from '@react-pdf/renderer'
import dayjs from '@/common/helpers/dayjs.js'

const containerStyles = {
  flexBasis: 0,
  flexGrow: 1,
  overflow: 'hidden',
  padding: '4pt 0 2pt',
}

const textStyles = {
  fontSize: '9pt',
  margin: '0 auto',
}

function renderDate(day) {
  return dayjs.utc(day.start).hour(0).minute(0).second(0).format('ddd LL')
}

function DayHeader({ day, styles }) {
  return (
    <View style={{ ...containerStyles, ...styles }}>
      <Text style={textStyles}>{renderDate(day)}</Text>
    </View>
  )
}

export default DayHeader
