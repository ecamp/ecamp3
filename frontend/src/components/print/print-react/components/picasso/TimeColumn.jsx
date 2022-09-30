// eslint-disable-next-line no-unused-vars
import React from 'react'
import { Text, View } from '@react-pdf/renderer'
import dayjs from '@/common/helpers/dayjs.js'
import picassoStyles from './picassoStyles.js'

function TimeColumn({ times, styles }) {
  return (
    <View style={{ ...styles, ...picassoStyles.timeColumn }}>
      {times.map(([time, weight]) => {
        return (
          <Text key={time} style={{ flexGrow: weight, ...picassoStyles.timeColumnText }}>
            {time !== times[0][0]
              ? dayjs()
                  .hour(0)
                  .minute(time * 60)
                  .second(0)
                  .format('LT')
              : ' '}
          </Text>
        )
      })}
    </View>
  )
}

export default TimeColumn
