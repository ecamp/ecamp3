<script>
import PdfComponent from '@/pdf/PdfComponent.js'
import RichTextList from './RichTextList.vue'
import HTML from 'html-parse-stringify'
import { decode } from 'html-entities'
import { h } from 'vue'

const BLOCK_TAGS = [
  'p',
  'h1',
  'h2',
  'h3',
  'h4',
  'h5',
  'h6',
  'ul',
  'ol',
  'li',
  'div',
  'blockquote',
  'pre',
  'table',
  'thead',
  'tbody',
  'tr',
  'td',
  'th',
  'hr',
]
const NBSP = '\u00a0'
const INLINE_CLASSES = {
  strong: 'bold',
  b: 'bold',
  em: 'italic',
  i: 'italic',
  u: 'underlined',
  s: 'strikethrough',
  strike: 'strikethrough',
  del: 'strikethrough',
}

function isTag(node, names) {
  return node.type === 'tag' && names.includes(node.name)
}

function isBlock(node) {
  return isTag(node, BLOCK_TAGS)
}

function isBlank(node) {
  return node.type === 'text' && !node.content.trim()
}

function endsWithBreak(nodes) {
  const last = nodes[nodes.length - 1]
  if (!last || last.type !== 'tag') return false
  return isTag(last, ['br']) || endsWithBreak(last.children)
}

function renderInline(node, context) {
  if (node.type === 'text') {
    return decode(node.content, { scope: 'strict' })
  }
  if (isTag(node, ['br'])) {
    return h('Text', {}, '\n')
  }
  const className = INLINE_CLASSES[node.name]
  return h(
    'Text',
    className ? { class: className } : {},
    renderInlineChildren(node, context)
  )
}

function renderInlineChildren(node, context) {
  if (!node.children.length) return [NBSP]
  return node.children.map((child) => renderInline(child, context))
}

function renderParagraph(nodes, context, className = 'p') {
  const children = nodes.map((node) => renderInline(node, context))
  if (!nodes.length || endsWithBreak(nodes)) children.push(NBSP)
  return h('Text', { class: className }, children)
}

function renderBlocks(nodes, context) {
  const blocks = []
  let inlineRun = []
  const flushInlineRun = () => {
    if (inlineRun.some((node) => !isBlank(node))) {
      blocks.push(renderParagraph(inlineRun, context))
    }
    inlineRun = []
  }
  for (const node of nodes) {
    if (isBlock(node)) {
      flushInlineRun()
      blocks.push(renderBlock(node, context))
    } else {
      inlineRun.push(node)
    }
  }
  flushInlineRun()
  return blocks
}

function renderBlock(node, context) {
  if (isTag(node, ['p'])) {
    return renderParagraph(node.children, context)
  }
  if (isTag(node, ['h1', 'h2', 'h3'])) {
    return renderParagraph(node.children, context, node.name)
  }
  if (isTag(node, ['ul', 'ol'])) {
    return h(RichTextList, { list: node, context, renderBlocks })
  }
  return h('View', {}, renderBlocks(node.children, context))
}

export default {
  name: 'RichText',
  extends: PdfComponent,
  props: {
    richText: { type: String, default: '' },
    fontSize: { type: Number, default: 10 },
  },
  computed: {
    parsed() {
      return HTML.parse(this.richText)
    },
  },
  render() {
    return renderBlocks(this.parsed, { fontSize: this.fontSize, depth: 0 })
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
