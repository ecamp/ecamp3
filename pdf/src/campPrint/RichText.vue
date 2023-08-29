<script>
import PdfComponent from '@/PdfComponent.js'
import HTML from 'html-parse-stringify'
import { decode } from 'html-entities'
// eslint-disable-next-line vue/prefer-import-from-vue
import { h } from '@vue/runtime-core'

function visit(node, parent = null) {
  const rule = rules.find((rule) => rule.shouldProcessNode(node, parent))
  if (!rule) {
    console.log('unknown HTML node type', node)
    return null
  }

  return rule.processNode(node, parent)
}

function visitChildren(children, parent) {
  return children.length
    ? children.map((child) => visit(child, parent))
    : [visit({ type: 'text', content: '&nbsp;' }, parent)]
}

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
    return [this.parsed].flat().map((node) => visit(node))
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
</pdf-style>
