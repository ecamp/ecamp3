// eslint-disable-next-line no-unused-vars
import React from 'react'
import pdf from '@react-pdf/renderer'
import { Parser } from 'html-to-react'

const { Text, View } = pdf

function addKeys (children) {
  return children.map((child, idx) => ({ ...child, key: idx }))
}

const richTextRules = [
  {
    shouldProcessNode: function (node) {
      return node.type === 'text'
    },
    processNode: function (node, children) {
      return <Text>{node.data}</Text>
    }
  },
  {
    replaceChildren: true,
    shouldProcessNode: function (node) {
      return node.type === 'tag' && node.name === 'p'
    },
    processNode: function (node, children) {
      return children.length ? <Text>{ addKeys(children) }</Text> : <Text> </Text>
    }
  },
  {
    replaceChildren: true,
    shouldProcessNode: function (node) {
      return node.type === 'tag' && (node.name === 'strong' || node.name === 'b')
    },
    processNode: function (node, children) {
      return <Text style={{ fontWeight: 'bold' }}>{ addKeys(children) }</Text>
    }
  },
  {
    replaceChildren: true,
    shouldProcessNode: function (node) {
      return node.type === 'tag' && node.name === 'ul'
    },
    processNode: function (node, children) {
      return children
    }
  },
  {
    replaceChildren: true,
    shouldProcessNode: function (node) {
      return node.type === 'tag' && node.name === 'ol' // TODO implement ordered list enumeration
    },
    processNode: function (node, children) {
      return children
    }
  },
  {
    replaceChildren: true,
    shouldProcessNode: function (node) {
      return node.type === 'tag' && node.name === 'li'
    },
    processNode: function (node, children) {
      return <Text style={{ marginLeft: '4pt' }}>• {children}</Text>
    }
  },
  {
    replaceChildren: true,
    shouldProcessNode: function (node) {
      return true
    },
    processNode: function (node, children) {
      console.log('unknown HTML node type', node, children)
      return <View/>
    }
  }
]

function RichText ({ richText }) {
  if (!richText) return <View/>
  const htmlToReactParser = new Parser()
  return htmlToReactParser.parseWithInstructions(richText, () => true, richTextRules)
}

export default RichText
