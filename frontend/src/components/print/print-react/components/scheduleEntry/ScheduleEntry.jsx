// eslint-disable-next-line no-unused-vars
import React from 'react'
import { Text, View } from '@react-pdf/renderer'
import styles from '../styles.js'
import Responsibles from '../picasso/Responsibles.jsx'
import CategoryLabel from '../CategoryLabel.jsx'
import dayjs from '@/common/helpers/dayjs.js'
import ContentNode from './contentNode/ContentNode.jsx'

const fontSize = 8

function ScheduleEntry(props) {
  const scheduleEntry = props.scheduleEntry
  const activity = scheduleEntry.activity()
  const start = dayjs.utc(scheduleEntry.start)
  const end = dayjs.utc(scheduleEntry.end)
  const startAt = start.format('ddd l LT')
  const endAt =
    start.format('ddd l') === end.format('ddd l')
      ? end.format('LT')
      : end.format('ddd l LT')

  return (
    <React.Fragment>
      <View wrap={false} minPresenceAhead={75}>
        <View
          style={{
            display: 'flex',
            flexDirection: 'row',
            borderBottom: '1px solid black',
            paddingBottom: '8pt',
          }}
        >
          <View
            style={{ ...styles.h1, display: 'flex', flexDirection: 'row', flexGrow: '1' }}
          >
            <Text
              id={props.id}
              bookmark={{
                title:
                  activity.category().short +
                  ' ' +
                  scheduleEntry.number +
                  ' ' +
                  activity.title,
                fit: true,
              }}
              style={{ margin: '4pt 0' }}
            >
              {scheduleEntry.number}{' '}
            </Text>
            <CategoryLabel activity={activity} style={{ margin: '4pt 0' }} />
            <Text style={{ margin: '4pt 0', overflow: 'ellipsis' }}>
              {' '}
              {activity.title}
            </Text>
          </View>
          <View
            style={{
              display: 'flex',
              flexDirection: 'column',
              justifyContent: 'center',
            }}
          >
            <View
              style={{
                display: 'flex',
                flexDirection: 'row',
                justifyContent: 'flex-end',
              }}
            >
              <Text>{startAt}</Text>
            </View>
            <View
              style={{
                display: 'flex',
                flexDirection: 'row',
                justifyContent: 'flex-end',
              }}
            >
              <Text>- {endAt}</Text>
            </View>
          </View>
        </View>
        <View style={{ display: 'flex', flexDirection: 'row' }}>
          <View
            style={{
              display: 'flex',
              flexDirection: 'column',
              flexShrink: '0',
              borderRight: '1px solid black',
            }}
          >
            <Text
              style={{
                height: fontSize + 8 + 'pt',
                borderBottom: '1px solid black',
                padding: '2pt 4pt 2pt 0',
              }}
            >
              {props.$tc('entity.activity.fields.location')}
            </Text>
            <Text
              style={{
                height: fontSize + 8 + 'pt',
                borderBottom: '1px solid black',
                padding: '2pt 4pt 2pt 0',
              }}
            >
              {props.$tc('entity.activity.fields.responsible')}
            </Text>
          </View>
          <View style={{ display: 'flex', flexDirection: 'column', flexGrow: '1' }}>
            <Text
              style={{
                height: fontSize + 8 + 'pt',
                borderBottom: '1px solid black',
                overflow: 'ellipsis',
                padding: '2pt 0 2pt 4pt',
              }}
            >
              {activity.location}
            </Text>
            <Responsibles
              styles={{
                height: fontSize + 8 + 'pt',
                borderBottom: '1px solid black',
                padding: '2pt 0 2pt 4pt',
              }}
              activity={activity}
            />
          </View>
        </View>
      </View>
      <View style={{ marginBottom: '20pt' }}>
        <ContentNode {...props} contentNode={activity.rootContentNode()} />
      </View>
    </React.Fragment>
  )
}

export default ScheduleEntry
