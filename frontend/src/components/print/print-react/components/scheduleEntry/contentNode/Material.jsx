// eslint-disable-next-line no-unused-vars
import React from 'react'
import { View, Text } from '@react-pdf/renderer'

const columnStyles = {
  flexGrow: '1',
  borderBottom: '1px solid black',
}

const column1Styles = {
  ...columnStyles,
  flexBasis: '7000pt',
  paddingRight: '2pt',
}

const column2Styles = {
  ...columnStyles,
  flexBasis: '3000pt',
  paddingLeft: '2pt',
}

function Material(props) {
  const material = props.contentNode
  const items = material.materialItems().items
  return (
    <View style={{ display: 'flex', flexDirection: 'column', marginBottom: '6pt' }}>
      <Text style={{ fontWeight: 'bold', borderBottom: '1px solid black' }}>
        {material.instanceName || props.$tc('contentNode.material.name')}
      </Text>
      {items.map((item) => {
        return (
          <View key={item.id} style={{ display: 'flex', flexDirection: 'row' }}>
            <View style={column1Styles}>
              <Text>
                {item.quantity} {item.unit} {item.article}
              </Text>
            </View>
            <View style={column2Styles}>
              <Text>{item.materialList().name}</Text>
            </View>
          </View>
        )
      })}
    </View>
  )
}

export default Material
