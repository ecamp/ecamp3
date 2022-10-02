// eslint-disable-next-line no-unused-vars
import React from 'react'
import { Text, View } from '@react-pdf/renderer'
import dayjs from '@/common/helpers/dayjs.js'
import picassoStyles from './picassoStyles.js'
import longestTime from './longestTime.js'

function TimeColumn({ times, styles, align = 'left' }) {
  const width = longestTime(times).length * 5 + 'pt' // we could do better than this heuristic...

  return (
    <View style={{ ...styles, ...picassoStyles.timeColumn(width) }}>
      <View style={picassoStyles.timeColumnAbsolutePositionContainer}>
        {times.map(([time, weight]) => {
          return (
            <View
              key={time}
              style={{
                ...picassoStyles.timeColumnRow,
                flexGrow: weight,
                alignItems: align === 'left' ? 'flex-start' : 'flex-end',
              }}
            >
              <Text style={picassoStyles.timeColumnText}>
                {time !== times[0][0]
                  ? dayjs()
                      .hour(0)
                      .minute(time * 60)
                      .second(0)
                      .format('LT')
                  : ' '}
              </Text>
            </View>
          )
        })}
      </View>
    </View>
  )
}

export default TimeColumn
