// eslint-disable-next-line no-unused-vars
import React from 'react'
import { View, Text } from '@react-pdf/renderer'
import dayjs from '@/common/helpers/dayjs.js'
import picassoStyles from './picassoStyles.js'
import campCollaborationDisplayName from '../../../../../common/helpers/campCollaborationDisplayName.js'

function renderDate(day) {
  return dayjs.utc(day.start).hour(0).minute(0).second(0).format('ddd LL')
}

function dayResponsibles(day, $tc) {
  const responsibles = day.dayResponsibles().items
  if (responsibles.length === 0) return ''
  const label = $tc('entity.day.fields.dayResponsibles')
  const displayNames = responsibles
    .map((responsible) => campCollaborationDisplayName(responsible.campCollaboration()))
    .join(', ')
  return `${label}: ${displayNames}`
}

function DayHeader({ day, showDayResponsibles, $tc, styles }) {
  return (
    <View style={{ ...picassoStyles.dayHeader, ...styles }}>
      <Text style={picassoStyles.dayHeaderText}>{renderDate(day)}</Text>
      {showDayResponsibles ? (
        <View style={picassoStyles.dayResponsibles}>
          <Text>{dayResponsibles(day, $tc)}</Text>
        </View>
      ) : (
        <React.Fragment />
      )}
    </View>
  )
}

export default DayHeader
