// eslint-disable-next-line no-unused-vars
import React from 'react'
import { Text, View } from '@react-pdf/renderer'
import dayjs from '@/common/helpers/dayjs.js'
import picassoStyles from './picassoStyles.js'

function longestTime(times) {
  return dayjs()
    .hour(0)
    .minute(times[times.length - 1][0] * 60)
    .second(0)
    .format('LT')
}

function TimeColumnSpacer({ times }) {
  return (
    <View style={{ ...picassoStyles.timeColumn, marginTop: 0, marginBottom: 0 }}>
      <Text style={{ ...picassoStyles.timeColumnText, opacity: 0 }}>
        {longestTime(times)}
      </Text>
    </View>
  )
}

export default TimeColumnSpacer
