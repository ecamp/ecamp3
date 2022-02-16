// eslint-disable-next-line no-unused-vars
import React from 'react'
import pdf from '@react-pdf/renderer'
import ColumnLayout from './ColumnLayout.jsx'
import LAThematicArea from './LAThematicArea.jsx'
import Storyboard from './Storyboard.jsx'
import Notes from './Notes.jsx'
import SafetyConcept from './SafetyConcept.jsx'
import Material from './Material.jsx'
import Storycontext from './Storycontext.jsx'

const { Text } = pdf

const contentNodeComponents = {
  ColumnLayout,
  LAThematicArea,
  Storyboard,
  Notes,
  SafetyConcept,
  Material,
  Storycontext
}

function ContentNode (props) {
  const contentTypeName = props.contentNode.contentType().name
  if (typeof contentNodeComponents[contentTypeName] !== 'undefined') {
    return React.createElement(contentNodeComponents[contentTypeName], { ...props, ContentNode })
  } else {
    return <Text>{contentTypeName}</Text>
  }
}

export default ContentNode
