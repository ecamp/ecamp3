// eslint-disable-next-line no-unused-vars
import React from 'react'
import { View, Text } from '@react-pdf/renderer'
import camelCase from 'lodash/camelCase.js'
import Checkmark from '../../Checkmark.jsx'

function LAThematicArea(props) {
  const laThematicArea = props.contentNode
  const options = laThematicArea.data.options
  const optionsArray = Object.keys(options).map((key) => ({
    translateKey: key,
    checked: options[key].checked,
  }))

  return (
    <View style={{ marginBottom: '6pt' }}>
      <Text style={{ fontWeight: 'bold' }}>
        {laThematicArea.instanceName ||
          props.$tc(`contentNode.${camelCase(laThematicArea.contentTypeName)}.name`)}
      </Text>
      {optionsArray.map((option) => {
        return (
          <View
            style={{ display: 'flex', flexDirection: 'row', alignItems: 'top' }}
            key={option.translateKey}
          >
            <Checkmark size={8} />
            <Text style={{ marginLeft: '2pt' }}>
              {' '}
              {props.$tc(
                `contentNode.laThematicArea.entity.option.${option.translateKey}.name`
              )}
            </Text>
          </View>
        )
      })}
    </View>
  )
}

export default LAThematicArea
