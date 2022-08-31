// eslint-disable-next-line no-unused-vars
import React from 'react'
import { View, Text } from '../../reactPdf.js'
import campCollaborationColor from '@/common/helpers/campCollaborationColor.js'
import campCollaborationInitials from '@/common/helpers/campCollaborationInitials.js'

const fontSize = 8
const initialFontSize = fontSize * 0.75

const avatarsStyles = {
  flexDirection: 'row',
  alignItems: 'flex-end',
}

const avatarStyles = {
  borderRadius: '50%',
  width: initialFontSize * 2 + 'pt',
  height: initialFontSize * 2 + 'pt',
  flexDirection: 'column',
  justifyContent: 'center',
}

const initialsStyles = {
  fontSize: initialFontSize + 'pt',
  textAlign: 'center',
  color: 'white',
}

function Responsibles({ activity, styles }) {
  const last = activity.activityResponsibles().items.length - 1
  return (
    <View style={{ ...avatarsStyles, ...styles }}>
      {activity.activityResponsibles().items.map((activityResponsible, index) => {
        return (
          <View
            key={activityResponsible.campCollaboration().id}
            style={{
              ...avatarStyles,
              backgroundColor: campCollaborationColor(
                activityResponsible.campCollaboration()
              ),
              ...(index === last ? {} : { marginRight: '-' + fontSize / 4 + 'px' }),
            }}
          >
            <Text style={initialsStyles}>
              {campCollaborationInitials(activityResponsible.campCollaboration())}
            </Text>
          </View>
        )
      })}
    </View>
  )
}

export default Responsibles
