// eslint-disable-next-line no-unused-vars
import React from 'react'
import pdf from '@react-pdf/renderer'
import RichText from '../../RichText.jsx'

const { View, Text } = pdf

const columnStyles = {
  flexGrow: '1'
}

const column1Styles = {
  ...columnStyles,
  flexBasis: '1000pt',
  paddingRight: '2pt'
}

const column2Styles = {
  ...columnStyles,
  flexBasis: '8000pt',
  borderLeft: '1px solid black',
  padding: '0 2pt'
}

const column3Styles = {
  ...columnStyles,
  flexBasis: '2000pt',
  borderLeft: '1px solid black',
  paddingLeft: '2pt'
}

function Storyboard (props) {
  const storyboard = props.contentNode
  const sections = storyboard.sections().items
  return <View style={{ display: 'flex', flexDirection: 'column', marginBottom: '6pt' }}>
    { storyboard.instanceName ? <Text style={{ fontWeight: 'bold' }}>{ storyboard.instanceName }</Text> : <View/> }
    <View style={{ display: 'flex', flexDirection: 'row', borderBottom: '1px solid black' }}>
      <View style={column1Styles}>
        <Text>{ props.$tc('contentNode.storyboard.entity.section.fields.column1') }</Text>
      </View>
      <View style={column2Styles}>
        <Text>{ props.$tc('contentNode.storyboard.entity.section.fields.column2') }</Text>
      </View>
      <View style={column3Styles}>
        <Text>{ props.$tc('contentNode.storyboard.entity.section.fields.column3') }</Text>
      </View>
    </View>
    { sections.map(section => {
      return <View key={section.id} style={{ display: 'flex', flexDirection: 'row' }}>
        <View style={column1Styles}>
          <RichText richText={section.column1}/>
        </View>
        <View style={column2Styles}>
          <RichText richText={section.column2}/>
        </View>
        <View style={column3Styles}>
          <RichText richText={section.column3}/>
        </View>
      </View>
    }) }
  </View>
}

export default Storyboard
