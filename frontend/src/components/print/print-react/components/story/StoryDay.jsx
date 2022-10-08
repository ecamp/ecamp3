// eslint-disable-next-line no-unused-vars
import React from 'react'
import { Text, View } from '@react-pdf/renderer'
import styles from '../styles.js'
import sortBy from 'lodash/sortBy.js'
import CategoryLabel from '../CategoryLabel.jsx'
import RichText from '../RichText.jsx'
import { dateLong } from '../../../../../common/helpers/dateHelperUTCFormatted.js'

function isEmptyHtml(html) {
  if (html === null) {
    return true
  }

  return html.trim() === '' || html.trim() === '<p></p>'
}

function StoryDay(props) {
  const sortedScheduleEntries = sortBy(
    props.period.scheduleEntries().items.filter((scheduleEntry) => {
      return scheduleEntry.day()._meta.self === props.day._meta.self
    }),
    (scheduleEntry) => scheduleEntry.start
  )
  const entries = sortedScheduleEntries.map((scheduleEntry) => ({
    scheduleEntry: scheduleEntry,
    storyChapters: props.period
      .contentNodes()
      .items.filter(
        (contentNode) =>
          contentNode.contentTypeName === 'Storycontext' &&
          contentNode.root()._meta.self ===
            scheduleEntry.activity().rootContentNode()._meta.self &&
          !isEmptyHtml(contentNode.data.text)
      ),
  }))
  const entriesWithStory = entries.filter(({ storyChapters }) => storyChapters.length)

  return (
    <React.Fragment>
      <Text
        id={`${props.id}-${props.period.id}-${props.day.id}`}
        style={{ ...styles.h2, marginTop: '12pt' }}
      >
        {props.$tc('entity.day.name')} {props.day.number} ({dateLong(props.day.start)})
      </Text>
      {entriesWithStory.map(({ scheduleEntry, storyChapters }) => {
        return (
          <React.Fragment key={scheduleEntry.id}>
            {storyChapters.map((chapter, idx) => {
              const chapterTitle =
                scheduleEntry.activity().title +
                (chapter.instanceName ? ' - ' + chapter.instanceName : '')
              return (
                <React.Fragment key={idx}>
                  <View
                    style={{
                      ...styles.h3,
                      display: 'flex',
                      flexDirection: 'row',
                      marginTop: '10pt',
                    }}
                    minPresenceAhead={30}
                  >
                    <Text id={`${props.id}-${props.period.id}-${scheduleEntry.id}`}>
                      {scheduleEntry.number}{' '}
                    </Text>
                    <CategoryLabel activity={scheduleEntry.activity()} />
                    <Text> {chapterTitle}</Text>
                  </View>
                  <RichText richText={chapter.data.text} />
                </React.Fragment>
              )
            })}
          </React.Fragment>
        )
      })}
    </React.Fragment>
  )
}

export default StoryDay
