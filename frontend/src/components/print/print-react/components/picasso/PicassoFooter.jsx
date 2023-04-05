// eslint-disable-next-line no-unused-vars
import React from 'react'
import { Text, View } from '@react-pdf/renderer'
import picassoStyles from './picassoStyles.js'
import campCollaborationLegalName from '@/common/helpers/campCollaborationLegalName.js'
import dayjs from '@/common/helpers/dayjs.js'

function joinWithoutBlanks(list, separator) {
  return list.filter((element) => !!element).join(separator)
}

function PicassoFooter({ period, locale, $tc }) {
  const camp = period.camp()
  const leaders = camp.campCollaborations().items.filter((campCollaboration) => {
    return (
      campCollaboration.status === 'established' && campCollaboration.role === 'manager'
    )
  })
  const leaderNames = leaders.map((campCollaboration) => {
    return campCollaborationLegalName(campCollaboration)
  })
  const leaderNameList = new Intl.ListFormat(locale, { style: 'short' }).format(
    leaderNames
  )
  const startDate = dayjs.utc(period.start).hour(0).minute(0).second(0)
  const endDate = dayjs.utc(period.end).hour(0).minute(0).second(0)
  const dates = dayjs.formatDatePeriod(
    startDate,
    endDate,
    $tc('global.datetime.dateLong'),
    locale
  )
  const address = joinWithoutBlanks(
    [
      camp.addressName,
      camp.addressStreet,
      joinWithoutBlanks([camp.addressZipcode, camp.addressCity], ' '),
    ],
    ', '
  )
  return (
    <View style={picassoStyles.picassoFooter}>
      <View style={picassoStyles.picassoFooterColumn}>
        {camp.courseKind || camp.kind ? (
          <Text>{joinWithoutBlanks([camp.courseKind, camp.kind], ', ')}</Text>
        ) : (
          <React.Fragment />
        )}
        {camp.courseNumber ? (
          <Text>
            {$tc('print.picasso.picassoFooter.courseNumber', {
              courseNumber: camp.courseNumber,
            })}
          </Text>
        ) : (
          <React.Fragment />
        )}
        {camp.motto ? (
          <Text style={{ alignSelf: 'flex-start' }}>{camp.motto}</Text>
        ) : (
          <React.Fragment />
        )}
      </View>
      <View style={picassoStyles.picassoFooterColumn}>
        {address ? <Text>{address}</Text> : <React.Fragment />}
        {dates ? <Text>{dates}</Text> : <React.Fragment />}
      </View>
      <View style={picassoStyles.picassoFooterColumn}>
        <Text>
          {$tc('print.picasso.picassoFooter.leaders', { leaders: leaderNameList })}
        </Text>
        {camp.coachName ? (
          <Text>
            {$tc('print.picasso.picassoFooter.coach', { coach: camp.coachName })}
          </Text>
        ) : (
          <React.Fragment />
        )}
        {camp.trainingAdvisorName ? (
          <Text>
            {$tc('print.picasso.picassoFooter.trainingAdvisor', {
              trainingAdvisor: camp.trainingAdvisorName,
            })}
          </Text>
        ) : (
          <React.Fragment />
        )}
      </View>
    </View>
  )
}

export default PicassoFooter
