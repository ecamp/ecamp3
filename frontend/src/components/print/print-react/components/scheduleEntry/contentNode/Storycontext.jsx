// eslint-disable-next-line no-unused-vars
import React from 'react'
import { View } from '@react-pdf/renderer'
import RichText from '../../RichText.jsx'
import InstanceName from '../InstanceName.jsx'

function Storycontext(props) {
  const storycontext = props.contentNode
  return (
    <View style={{ marginBottom: '6pt' }}>
      <InstanceName contentNode={storycontext} $tc={props.$tc} />
      <RichText richText={storycontext.data.html} />
    </View>
  )
}

export default Storycontext
