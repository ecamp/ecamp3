<script>
import PdfComponent from '@/pdf/PdfComponent.js'
import HTML from 'html-parse-stringify'
import { decode } from 'html-entities'
import { h } from 'vue'

function visit(node, parent = null, images = {}) {
  const rule = rules.find((rule) => rule.shouldProcessNode(node, parent))
  if (!rule) {
    console.log('unknown HTML node type', node)
    return null
  }

  return rule.processNode(node, parent, images)
}

function visitChildren(children, parent, images = {}) {
  return children.length
    ? children.map((child) => visit(child, parent, images))
    : [visit({ type: 'text', content: '&nbsp;' }, parent)]
}

function prependTextContent(node, prefix) {
  if (node.type === 'text') {
    node.content = prefix + (node.content || '')
    return true
  }
  if (node.children?.length) {
    for (const child of node.children) {
      if (prependTextContent(child, prefix)) return true
    }
  }
  return false
}

function splitImageParents(node) {
  if (node.type !== 'tag' || node.name === 'img') return [node]
  const result = []
  let children = []
  const flush = () => {
    if (children.length) result.push({ ...node, children })
    children = []
  }
  for (const child of (node.children ?? []).flatMap(splitImageParents)) {
    if (child.type === 'tag' && child.name === 'img') {
      flush()
      result.push(child)
    } else {
      children.push(child)
    }
  }
  flush()
  return result.length ? result : [node]
}

const rules = [
  {
    shouldProcessNode: (node) => node.type === 'tag' && node.name === 'img',
    processNode: (node, _, images) => {
      const src = images[node.attrs.src]
      if (!src) throw new Error('Image is missing from PDF preparation')
      const width = Math.min(Number(node.attrs.width) || 400, 400) * 0.75
      return h('Image', {
        src,
        style: { width, maxWidth: '100%', maxHeight: 400, objectFit: 'contain' },
      })
    },
  },
  {
    shouldProcessNode: (node) => node.type === 'text',
    processNode: (node) => decode(node.content, { scope: 'strict' }),
  },
  {
    shouldProcessNode: (node) => node.type === 'tag' && node.name === 'p',
    processNode: (node, _, images) => {
      const children = (node.children ?? []).flatMap(splitImageParents)
      const output = []
      let text = []
      const flush = () => {
        if (text.length) output.push(h('Text', { class: 'p' }, text))
        text = []
      }
      for (const child of children) {
        if (child.type === 'tag' && child.name === 'img') {
          flush()
          output.push(visit(child, node, images))
        } else {
          text.push(visit(child, node, images))
        }
      }
      flush()
      return output.length
        ? output
        : h('Text', { class: 'p' }, visitChildren(children, node, images))
    },
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
    processNode: (node, _, images) =>
      h('View', visitChildren(node.children, node, images)),
  },
  {
    shouldProcessNode: (node) => node.type === 'tag' && node.name === 'li',
    processNode: (node, parent, images) => {
      const emptyChild = {
        type: 'text',
        attrs: {},
        children: [],
      }
      if (!node.children.length) {
        node.children.push({ ...emptyChild })
      }

      let prefix = ''
      if (parent.name === 'ul') {
        prefix = '• '
      } else if (parent.name === 'ol') {
        const number = calculateListNumber(node, parent)
        prefix = `${number}. `
      }

      if (prefix) {
        prependTextContent(node, prefix)
      }

      return h(
        'View',
        { style: { marginLeft: '4pt' } },
        visitChildren(node.children, node, images)
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
  inject: { pdfImages: { default: () => ({}) } },
  props: {
    richText: { type: String, default: '' },
  },
  computed: {
    parsed() {
      return HTML.parse(this.richText)
    },
  },
  render() {
    return [this.parsed].flat().map((node) => visit(node, null, this.pdfImages))
  },
}
</script>
<style lang="react-pdf">
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
</style>
