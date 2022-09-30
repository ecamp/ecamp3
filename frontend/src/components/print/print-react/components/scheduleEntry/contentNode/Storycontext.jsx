// eslint-disable-next-line no-unused-vars
import React from 'react'
import { View, Text } from '@react-pdf/renderer'
import RichText from '../../RichText.jsx'

function Storycontext(props) {
  const storycontext = props.contentNode
  return (
    <View style={{ marginBottom: '6pt' }}>
      {storycontext.instanceName ? (
        <Text style={{ fontWeight: 'bold' }}>{storycontext.instanceName}</Text>
      ) : (
        <View />
      )}
      <RichText richText={storycontext.data.text} />
    </View>
  )
}

export default Storycontext
