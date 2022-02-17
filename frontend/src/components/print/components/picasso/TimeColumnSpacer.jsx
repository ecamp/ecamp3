// eslint-disable-next-line no-unused-vars
import React from 'react'
import { Text, View } from '../../reactPdf.js'
import dayjs from '../../../../../../common/helpers/dayjs.js'

const fontSize = 8

const columnStyles = {
  flexGrow: 0,
  flexShrink: 0,
  display: 'flex',
  flexDirection: 'column',
  alignItems: 'stretch',
  opacity: '0'
}
const rowStyles = {
  paddingHorizontal: '2pt',
  fontSize: fontSize + 'pt',
  flexBasis: 1
}

function longestTime (times) {
  return dayjs().hour(0).minute(times[times.length - 1][0] * 60).second(0).format('LT')
}

function TimeColumnSpacer ({ times }) {
  return <View style={ columnStyles }>
    <Text style={ rowStyles }>{ longestTime(times) }</Text>
  </View>
}

export default TimeColumnSpacer
