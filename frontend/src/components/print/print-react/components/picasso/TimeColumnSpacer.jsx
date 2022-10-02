// eslint-disable-next-line no-unused-vars
import React from 'react'
import { Text, View } from '@react-pdf/renderer'
import picassoStyles from './picassoStyles.js'
import longestTime from './longestTime.js'

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
