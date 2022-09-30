// eslint-disable-next-line no-unused-vars
import React from 'react'
import { View, Text } from '@react-pdf/renderer'
import RichText from '../../RichText.jsx'

function Notes(props) {
  const notes = props.contentNode
  return (
    <View style={{ marginBottom: '6pt' }}>
      {notes.instanceName ? (
        <Text style={{ fontWeight: 'bold' }}>{notes.instanceName}</Text>
      ) : (
        <View />
      )}
      <RichText richText={notes.data.text} />
    </View>
  )
}

export default Notes
