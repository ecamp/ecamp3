<script>
import PdfComponent from '@/PdfComponent.js'
import HTML from 'html-parse-stringify'
import { decode } from 'html-entities'
// eslint-disable-next-line vue/prefer-import-from-vue
import { h } from '@vue/runtime-core'

function visit(node, parent = null, index = 0) {
  const rule = rules.find((rule) => rule.shouldProcessNode(node, parent))
  if (!rule) {
    console.log('unknown HTML node type', node)
    return null
  }

  return rule.processNode(node, parent, index)
}

function visitChildren(children, parent) {
  return children.length
    ? children.map((child, idx) => visit(child, parent, idx))
    : [visit({ type: 'text', content: '&nbsp;' }, parent, 0)]
}

const tableContextStack = []

const rules = [
  {
    shouldProcessNode: (node) => node.type === 'text',
    processNode: (node) => decode(node.content, { scope: 'strict' }),
  },
  {
    shouldProcessNode: (node) => node.type === 'tag' && node.name === 'p',
    processNode: (node) => h('Text', { class: 'p' }, visitChildren(node.children, node)),
  },
  {
    shouldProcessNode: (node) => node.type === 'tag' && node.name === 'br',
    processNode: (_) => h('Text', {}, '\n'),
  },
  {
    shouldProcessNode: (node) =>
      node.type === 'tag' && ['h1', 'h2', 'h3'].includes(node.name),
    processNode: function (node) {
      return h('Text', { class: node.name }, visitChildren(node.children, node))
    },
  },
  {
    shouldProcessNode: (node) =>
      node.type === 'tag' && (node.name === 'strong' || node.name === 'b'),
    processNode: (node) =>
      h('Text', { class: 'bold' }, visitChildren(node.children, node)),
  },
  {
    shouldProcessNode: (node) => node.type === 'tag' && node.name === 'em',
    processNode: (node) =>
      h('Text', { class: 'italic' }, visitChildren(node.children, node)),
  },
  {
    shouldProcessNode: (node) => node.type === 'tag' && node.name === 'u',
    processNode: (node) =>
      h('Text', { class: 'underlined' }, visitChildren(node.children, node)),
  },
  {
    shouldProcessNode: (node) =>
      node.type === 'tag' && (node.name === 's' || node.name === 'strike'),
    processNode: (node) =>
      h('Text', { class: 'strikethrough' }, visitChildren(node.children, node)),
  },
  {
    shouldProcessNode: (node) => node.type === 'tag' && ['ul', 'ol'].includes(node.name),
    processNode: (node) => h('View', visitChildren(node.children, node)),
  },
  {
    shouldProcessNode: (node) => node.type === 'tag' && node.name === 'li',
    processNode: (node, parent) => {
      const emptyChild = {
        type: 'text',
        attrs: {},
        children: [],
      }
      if (!node.children.length) {
        node.children.push({ ...emptyChild })
      }
      if (!node.children[0].children.length) {
        node.children[0].children.push({ ...emptyChild, content: '' })
      }
      if (parent.name === 'ul') {
        node.children[0].children[0].content = '• ' + node.children[0].children[0].content
      } else if (parent.name === 'ol') {
        const number = calculateListNumber(node, parent)
        node.children[0].children[0].content =
          `${number}. ` + node.children[0].children[0].content
      }
      return h(
        'View',
        { style: { marginLeft: '4pt' } },
        visitChildren(node.children, node)
      )
    },
  },
  {
    shouldProcessNode: (node) => node.type === 'tag' && node.name === 'table',
    processNode: (node) => {
      tableContextStack.push([])
      const result = h('View', { class: 'table' }, visitChildren(node.children, node))
      tableContextStack.pop()
      return result
    },
  },
  {
    shouldProcessNode: (node) => node.type === 'tag' && node.name === 'colgroup',
    processNode: (node) => {
      visitChildren(node.children, node)
      return null
    },
  },
  {
    shouldProcessNode: (node) => node.type === 'tag' && node.name === 'col',
    processNode: (node) => {
      const width = Math.floor(
        parseInt(node.attrs.style?.match(/width:\s*(\d+)px;/)[1]) / 1.33
      )
      const tableContext = tableContextStack.pop()
      tableContext.push(width)
      tableContextStack.push(tableContext)
      return null
    },
  },
  {
    shouldProcessNode: (node) => node.type === 'tag' && node.name === 'tbody',
    processNode: (node) =>
      h('View', { class: 'tbody' }, visitChildren(node.children, node)),
  },
  {
    shouldProcessNode: (node) => node.type === 'tag' && node.name === 'tr',
    processNode: (node) => h('View', { class: 'tr' }, visitChildren(node.children, node)),
  },
  {
    shouldProcessNode: (node) =>
      node.type === 'tag' && (node.name === 'td' || node.name === 'th'),
    processNode: (node, _, index) => {
      const width = tableContextStack[tableContextStack.length - 1][index]
      const style = width
        ? { flexBasis: width, flexGrow: 0, flexShrink: 0 }
        : { flexBasis: 1.33, flexGrow: 1 }
      return h('View', { class: node.name, style }, visitChildren(node.children, node))
    },
  },
]

function calculateListNumber(node, parent) {
  const index = parent.children
    .filter((child) => child.type === 'tag' && child.name === 'li')
    .indexOf(node)
  if (parent.attrs.start !== undefined) {
    const start = parseInt(parent.attrs.start)
    if (!isNaN(start)) return start + index
  }
  return index + 1
}

export default {
  name: 'RichText',
  extends: PdfComponent,
  props: {
    richText: { type: String, default: '' },
  },
  computed: {
    parsed() {
      return HTML.parse(this.richText)
    },
  },
  render() {
    return [this.parsed].flat().map((node, idx) => visit(node, null, idx))
  },
}
</script>
<pdf-style>
.p {
  margin-bottom: 2pt;
}
.bold {
  font-weight: bold;
}
.italic {
  font-style: italic;
}
.underlined {
  text-decoration: underline;
}
.strikethrough {
  text-decoration: line-through;
}
.table {
  borderLeft: 1pt solid black;
  borderTop: 1pt solid black;
  width: 100%;
}
.tr {
  flex-direction: row;
  align-items: stretch;
  width: 100%;
}
.th {
  font-weight: bold;
  background-color: #f1f3f5;
  border-right: 1pt solid black;
  border-bottom: 1pt solid black;
  padding: 2pt 4pt 0;
  flex-grow: 1;
  flex-basis: 1;
}
.td {
  border-right: 1pt solid black;
  border-bottom: 1pt solid black;
  padding: 2pt 4pt 0;
  flex-grow: 1;
  flex-basis: 1;
}
</pdf-style>
