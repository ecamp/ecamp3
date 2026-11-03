<script>
import PdfComponent from '@/pdf/PdfComponent.js'
import { h } from 'vue'

const BULLETS = ['•', '◦', '▪']
const EM_WIDTHS = { digit: 0.62, bullet: 0.5, letter: 0.55, punctuation: 0.25 }
const MARKER_GAP_EM = 0.4
const MIN_MARKER_COLUMN_EM = 1.5
const EMPTY_PARAGRAPH = { type: 'tag', name: 'p', attrs: {}, children: [] }

const NUMBER_FORMATS = {
  1: (number) => String(number),
  a: (number) => alphabetic(number),
  A: (number) => alphabetic(number).toUpperCase(),
  i: (number) => roman(number),
  I: (number) => roman(number).toUpperCase(),
}

function alphabetic(number) {
  let result = ''
  for (let n = number; n > 0; n = Math.floor((n - 1) / 26)) {
    result = String.fromCharCode(97 + ((n - 1) % 26)) + result
  }
  return result
}

const ROMAN_NUMERALS = [
  [1000, 'm'],
  [900, 'cm'],
  [500, 'd'],
  [400, 'cd'],
  [100, 'c'],
  [90, 'xc'],
  [50, 'l'],
  [40, 'xl'],
  [10, 'x'],
  [9, 'ix'],
  [5, 'v'],
  [4, 'iv'],
  [1, 'i'],
]

function roman(number) {
  let result = ''
  let remainder = number
  for (const [value, numeral] of ROMAN_NUMERALS) {
    while (remainder >= value) {
      result += numeral
      remainder -= value
    }
  }
  return result
}

function listMarkers(list, items, depth) {
  if (list.name === 'ul') {
    const bullet = BULLETS[Math.min(depth, BULLETS.length - 1)]
    return items.map(() => bullet)
  }
  const format = NUMBER_FORMATS[list.attrs.type] || NUMBER_FORMATS['1']
  let counter = parseInt(list.attrs.start)
  if (isNaN(counter)) counter = 1
  return items.map((item) => {
    const value = parseInt(item.attrs.value)
    if (!isNaN(value)) counter = value
    return `${format(counter++)}.`
  })
}

function markerColumnWidth(markers, fontSize) {
  const longest = Math.max(...markers.map(estimateWidthInEm))
  return Math.max(MIN_MARKER_COLUMN_EM, longest + MARKER_GAP_EM) * fontSize
}

function estimateWidthInEm(marker) {
  return [...marker].reduce((width, character) => {
    if (/\d/.test(character)) return width + EM_WIDTHS.digit
    if (BULLETS.includes(character)) return width + EM_WIDTHS.bullet
    if (/[a-z]/i.test(character)) return width + EM_WIDTHS.letter
    return width + EM_WIDTHS.punctuation
  }, 0)
}

export default {
  name: 'RichTextList',
  extends: PdfComponent,
  props: {
    list: { type: Object, required: true },
    context: { type: Object, required: true },
    renderBlocks: { type: Function, required: true },
  },
  computed: {
    items() {
      return this.list.children.filter(
        (child) => child.type === 'tag' && child.name === 'li'
      )
    },
    markers() {
      return listMarkers(this.list, this.items, this.context.depth)
    },
    markerStyle() {
      return {
        width: markerColumnWidth(this.markers, this.context.fontSize),
        paddingRight: MARKER_GAP_EM * this.context.fontSize,
      }
    },
    itemContext() {
      return { ...this.context, depth: this.context.depth + 1 }
    },
  },
  methods: {
    itemContent(item) {
      const blocks = this.renderBlocks(item.children, this.itemContext)
      return blocks.length
        ? blocks
        : this.renderBlocks([EMPTY_PARAGRAPH], this.itemContext)
    },
  },
  render() {
    return h(
      'View',
      { class: 'list' },
      this.items.map((item, index) =>
        h('View', { class: 'list-item' }, [
          h(
            'Text',
            { class: 'list-item-marker', style: this.markerStyle },
            this.markers[index]
          ),
          h('View', { class: 'list-item-content' }, this.itemContent(item)),
        ])
      )
    )
  },
}
</script>
<style lang="react-pdf">
.list {
  display: flex;
  flex-direction: column;
}
.list-item {
  display: flex;
  flex-direction: row;
}
.list-item-marker {
  flex-shrink: 0;
  text-align: right;
  font-feature-settings: 'tnum';
}
.list-item-content {
  flex-grow: 1;
  flex-shrink: 1;
  flex-basis: 0;
}
</style>
