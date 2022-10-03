// eslint-disable-next-line no-unused-vars
import React from 'react'
import { View } from '@react-pdf/renderer'
import RichText from '../../RichText.jsx'
import InstanceName from '../InstanceName.jsx'

function SafetyConcept(props) {
  const safetyConcept = props.contentNode

  return (
    <View style={{ marginBottom: '6pt' }}>
      <InstanceName contentNode={safetyConcept} $tc={props.$tc} />
      <RichText richText={safetyConcept.data.text} />
    </View>
  )
}

export default SafetyConcept
