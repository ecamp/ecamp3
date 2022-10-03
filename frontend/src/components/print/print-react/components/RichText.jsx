// eslint-disable-next-line no-unused-vars
import React from 'react'
import { Text, View } from '@react-pdf/renderer'
import htmlToReact from 'html-to-react'

function addKeys(children) {
  return children.map((child, idx) => ({ ...child, key: idx }))
}

const richTextRules = [
  {
    shouldProcessNode: function (node) {
      return node.type === 'text'
    },
    processNode: function (node) {
      return <Text>{node.data}</Text>
    },
  },
  {
    replaceChildren: true,
    shouldProcessNode: function (node) {
      return node.type === 'tag' && node.name === 'p'
    },
    processNode: function (node, children) {
      return children.length ? <Text>{addKeys(children)}</Text> : <Text> </Text>
    },
  },
  {
    replaceChildren: true,
    shouldProcessNode: function (node) {
      return node.type === 'tag' && (node.name === 'strong' || node.name === 'b')
    },
    processNode: function (node, children) {
      return <Text style={{ fontWeight: 'bold' }}>{addKeys(children)}</Text>
    },
  },
  {
    replaceChildren: true,
    shouldProcessNode: function (node) {
      return (
        node.type === 'tag' &&
        (node.name === 'h1' || node.name === 'h2' || node.name === 'h3')
      )
    },
    processNode: function (node, children) {
      return (
        <Text
          style={{
            fontWeight: 'bold',
            fontSize: 16 - 2 * parseInt(node.name.charAt(1)),
          }}
        >
          {addKeys(children)}
        </Text>
      )
    },
  },
  {
    replaceChildren: true,
    shouldProcessNode: function (node) {
      return node.type === 'tag' && node.name === 'em'
    },
    processNode: function (node, children) {
      return <Text style={{ fontStyle: 'italic' }}>{addKeys(children)}</Text>
    },
  },
  {
    replaceChildren: true,
    shouldProcessNode: function (node) {
      return node.type === 'tag' && node.name === 'u'
    },
    processNode: function (node, children) {
      return <Text style={{ textDecoration: 'underline' }}>{addKeys(children)}</Text>
    },
  },
  {
    replaceChildren: true,
    shouldProcessNode: function (node) {
      return node.type === 'tag' && node.name === 's'
    },
    processNode: function (node, children) {
      return <Text style={{ textDecoration: 'line-through' }}>{addKeys(children)}</Text>
    },
  },
  {
    replaceChildren: true,
    shouldProcessNode: function (node) {
      return node.type === 'tag' && node.name === 'ul'
    },
    processNode: function (node, children) {
      return children
    },
  },
  {
    replaceChildren: true,
    shouldProcessNode: function (node) {
      return node.type === 'tag' && node.name === 'ol' // TODO implement ordered list enumeration
    },
    processNode: function (node, children) {
      return children
    },
  },
  {
    replaceChildren: true,
    shouldProcessNode: function (node) {
      return node.type === 'tag' && node.name === 'li'
    },
    processNode: function (node, children) {
      return <Text style={{ marginLeft: '4pt' }}>• {children}</Text>
    },
  },

  // fall back tag --> print as plain text
  {
    replaceChildren: true,
    shouldProcessNode: function (node) {
      return node.type === 'tag'
    },
    processNode: function (node, children) {
      console.log('unknown HTML node tag', node, children)
      return <Text>{addKeys(children)}</Text>
    },
  },
  {
    replaceChildren: true,
    shouldProcessNode: function () {
      return true
    },
    processNode: function (node, children) {
      console.log('unknown HTML node type', node, children)
      return <React.Fragment />
    },
  },
]

function RichText({ richText }) {
  if (!richText) return <React.Fragment />
  const htmlToReactParser = new htmlToReact.Parser()
  return htmlToReactParser.parseWithInstructions(richText, () => true, richTextRules)
}

export default RichText
