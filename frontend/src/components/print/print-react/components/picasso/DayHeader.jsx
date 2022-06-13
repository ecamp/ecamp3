// eslint-disable-next-line no-unused-vars
import React from 'react'
import { View, Text } from '../../reactPdf.js'
import dayjs from '@/common/helpers/dayjs.js'

const wrapperStyles = {
  flexGrow: '1',
  display: 'flex',
  flexDirection: 'column',
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
    <View style={{ ...wrapperStyles, ...styles }}>
      <Text style={textStyles}>{renderDate(day)}</Text>
    </View>
  )
}

export default DayHeader
