// eslint-disable-next-line no-unused-vars
import React from 'react'
import pdf from '@react-pdf/renderer'
import RichText from '../../RichText.jsx'

const { View, Text } = pdf

function SafetyConcept (props) {
  const safetyConcept = props.contentNode
  return <View style={{ marginBottom: '6pt' }}>
    { safetyConcept.instanceName ? <Text style={{ fontWeight: 'bold' }}>{ safetyConcept.instanceName }</Text> : <View/> }
    <RichText richText={safetyConcept.text}/>
  </View>
}

export default SafetyConcept
