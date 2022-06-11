// eslint-disable-next-line no-unused-vars
import React from 'react'
import { View } from '../../../reactPdf.js'

function ColumnLayout (props) {
  const ContentNode = props.ContentNode
  const columns = props.contentNode.data.columns
  const firstSlot = columns.length ? columns[0].slot : '1'
  const lastSlot = columns.length ? columns[columns.length - 1].slot : '1'
  const children = props.allContentNodes.items.filter(contentNode => {
    return contentNode.parent && contentNode.parent()._meta.self === props.contentNode._meta.self
  })

  return <View style={{ display: 'flex', flexDirection: 'row' }}>
      { columns.map(({ slot, width }) => {
        return <View key={slot} style={{
          borderLeft: (slot === firstSlot) ? 'none' : '1px solid black',
          padding: '2pt ' + (slot === lastSlot ? '0' : '1%') + ' 2pt ' + (slot === firstSlot ? '0' : '1%'),
          flexBasis: (width * 1000) + 'pt'
        }}>
          { children
            .filter(child => child.slot === slot)
            .sort((child1, child2) => parseInt(child1.position) - parseInt(child2.position))
            .map(child => {
              return <ContentNode {...props} contentNode={child} key={child.id}/>
            }) }
        </View>
      }) }
    </View>
}

export default ColumnLayout
