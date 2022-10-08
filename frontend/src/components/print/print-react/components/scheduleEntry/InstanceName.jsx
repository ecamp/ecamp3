// eslint-disable-next-line no-unused-vars
import React from 'react'
import { Text } from '@react-pdf/renderer'
import camelCase from 'lodash/camelCase.js'

function InstanceName({ contentNode, $tc }) {
  return (
    <Text style={{ fontWeight: 'bold' }}>
      {contentNode.instanceName ||
        $tc(`contentNode.${camelCase(contentNode.contentTypeName)}.name`)}
    </Text>
  )
}

export default InstanceName
