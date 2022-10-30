// eslint-disable-next-line no-unused-vars
import React from 'react'
import { Text, View } from '@react-pdf/renderer'
import RichText from '../../RichText.jsx'
import InstanceName from '../InstanceName.jsx'

const columnStyles = {
  flexGrow: '1',
  lineHeight: 1.6,
  paddingBottom: '4pt',
}

const column1Styles = {
  ...columnStyles,
  flexBasis: '23pt',
  flexShrink: '0',
  flexGrow: '0',
  paddingRight: '2pt',
  overflow: 'hidden',
}

const column2Styles = {
  ...columnStyles,
  flexBasis: 0,
  flexGrow: '1',
  borderLeft: '1px solid black',
  paddingHorizontal: '2pt',
}

const column3Styles = {
  ...columnStyles,
  flexBasis: '40pt',
  flexShrink: '0',
  flexGrow: '0',
  borderLeft: '1px solid black',
  paddingLeft: '2pt',
  overflow: 'hidden',
}

function Storyboard(props) {
  const storyboard = props.contentNode
  const sections = storyboard.data.sections
  const sectionsArray = Object.keys(sections).map((key) => ({
    key,
    column1: sections[key].column1,
    column2Html: sections[key].column2Html,
    column3: sections[key].column3,
    position: sections[key].position,
  }))
  return (
    <View style={{ display: 'flex', flexDirection: 'column', marginBottom: '6pt' }}>
      <InstanceName contentNode={storyboard} $tc={props.$tc} />
      <View
        style={{
          display: 'flex',
          flexDirection: 'row',
          borderBottom: '1px solid black',
        }}
      >
        <View style={{ ...column1Styles, paddingBottom: 0 }}>
          <Text>{props.$tc('contentNode.storyboard.entity.section.fields.column1')}</Text>
        </View>
        <View style={{ ...column2Styles, paddingBottom: 0 }}>
          <Text>
            {props.$tc('contentNode.storyboard.entity.section.fields.column2Html')}
          </Text>
        </View>
        <View style={{ ...column3Styles, paddingBottom: 0 }}>
          <Text>{props.$tc('contentNode.storyboard.entity.section.fields.column3')}</Text>
        </View>
      </View>
      {sectionsArray
        .sort((section1, section2) => section1.position - section2.position)
        .map((section) => {
          return (
            <View key={section.key} style={{ display: 'flex', flexDirection: 'row' }}>
              <View style={column1Styles}>
                <Text>{section.column1}</Text>
              </View>
              <View style={column2Styles}>
                <RichText richText={section.column2Html} />
              </View>
              <View style={column3Styles}>
                <Text>{section.column3}</Text>
              </View>
            </View>
          )
        })}
    </View>
  )
}

export default Storyboard
