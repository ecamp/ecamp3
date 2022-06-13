// eslint-disable-next-line no-unused-vars
import React from 'react'
import { View, Text } from '../../reactPdf.js'

const fontSize = 8
const initialFontSize = fontSize * 0.75

function color (campCollaboration) {
  const h = parseInt(campCollaboration.user()?.id || campCollaboration.id, 16) % 360
  return `hsl(${h}, 100%, 30%)`
}

function initials (campCollaboration) {
  const displayName =
    campCollaboration.user()?.displayName ||
    campCollaboration.inviteEmail.split('@', 2).shift()
  let items = displayName.split(' ', 2)
  if (items.length === 1) {
    items = items.shift().split(/[,._-]/, 2)
  }
  if (items.length === 1) {
    return displayName.substr(0, 2).toUpperCase()
  } else {
    return items[0].substr(0, 1) + items[1].substr(0, 1).toUpperCase()
  }
}

const avatarsStyles = {
  flexDirection: 'row',
  alignItems: 'flex-end'
}

const avatarStyles = {
  borderRadius: '50%',
  width: initialFontSize * 2 + 'pt',
  height: initialFontSize * 2 + 'pt',
  flexDirection: 'column',
  justifyContent: 'center'
}

const initialsStyles = {
  fontSize: initialFontSize + 'pt',
  textAlign: 'center',
  color: 'white'
}

function Responsibles ({ activity, styles }) {
  const last = activity.activityResponsibles().items.length - 1
  return (
    <View style={{ ...avatarsStyles, ...styles }}>
      {activity.activityResponsibles().items.map((activityResponsible, index) => {
        return (
          <View
            key={activityResponsible.campCollaboration().id}
            style={{
              ...avatarStyles,
              backgroundColor: color(activityResponsible.campCollaboration()),
              ...(index === last ? {} : { marginRight: '-' + fontSize / 4 + 'px' })
            }}
          >
            <Text style={initialsStyles}>
              {initials(activityResponsible.campCollaboration())}
            </Text>
          </View>
        )
      })}
    </View>
  )
}

export default Responsibles
