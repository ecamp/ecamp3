// eslint-disable-next-line no-unused-vars
import React from 'react'
import pdf from '@react-pdf/renderer'

const { Text } = pdf

function RichText ({ richText }) {
  const strippedText = richText.replaceAll(/<[^>]+>/ig, '')
  return <Text>{ strippedText }</Text>
}

export default RichText
