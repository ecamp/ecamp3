// eslint-disable-next-line no-unused-vars
import React from 'react'
import { View, Text } from '../../../reactPdf.js'
import camelCase from 'lodash/camelCase.js'
import Checkmark from '../../Checkmark.jsx'

function LAThematicArea (props) {
  const laThematicArea = props.contentNode
  const options = laThematicArea.options().items.filter(item => item.checked)
  return <View style={{ marginBottom: '6pt' }}>
    <Text style={{ fontWeight: 'bold' }}>{ laThematicArea.instanceName || props.$tc(`contentNode.${camelCase(laThematicArea.contentTypeName)}.name`) }</Text>
    { options.map(option => {
      return <View style={{ display: 'flex', flexDirection: 'row', alignItems: 'top' }} key={option.id}>
        <Checkmark size={8}/><Text style={{ marginLeft: '2pt' }}> { props.$tc(`contentNode.laThematicArea.entity.option.${option.translateKey}.name`) }</Text>
      </View>
    }) }
  </View>
}

export default LAThematicArea
