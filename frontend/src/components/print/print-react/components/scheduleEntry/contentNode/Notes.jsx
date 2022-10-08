// eslint-disable-next-line no-unused-vars
import React from 'react'
import { View } from '@react-pdf/renderer'
import RichText from '../../RichText.jsx'
import InstanceName from '../InstanceName.jsx'

function Notes(props) {
  const notes = props.contentNode
  return (
    <View style={{ marginBottom: '6pt' }}>
      <InstanceName contentNode={notes} $tc={props.$tc} />
      <RichText richText={notes.data.text} />
    </View>
  )
}

export default Notes
