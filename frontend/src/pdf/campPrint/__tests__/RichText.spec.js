import { describe, expect, it } from 'vitest'
import { h } from 'vue'
import { renderVueToPdfStructure } from '@/pdf/renderer/vueRenderer.js'
import RichText from '../RichText.vue'

function render(richText) {
  const document = renderVueToPdfStructure({
    render: () => h('Document', {}, [h('Page', {}, [h(RichText, { richText })])]),
  })
  return document.children[0].children
}

function textContent(node) {
  if (node.type === 'TEXT_INSTANCE') return node.value.replaceAll('\u00a0', '␣')
  return node.children.map(textContent).join('')
}

function lines(nodes, indent = '') {
  return nodes.flatMap((node) => {
    if (node.type === 'TEXT') {
      return textContent(node)
        .split('\n')
        .map((line) => indent + line)
    }
    if (node.style.flexDirection === 'row') {
      const [marker, content] = node.children
      const [first, ...rest] = lines(content.children, '')
      const markerText = textContent(marker).padEnd(5)
      return [
        indent + markerText + first,
        ...rest.map((line) => indent + ' '.repeat(5) + line),
      ]
    }
    return lines(node.children, indent)
  })
}

function layout(richText) {
  return lines(render(richText)).join('\n')
}

function markers(nodes) {
  return nodes.flatMap((node) => {
    if (node.type !== 'VIEW') return []
    if (node.style.flexDirection === 'row') {
      const [marker, content] = node.children
      return [
        { text: textContent(marker), width: marker.style.width },
        ...markers(content.children),
      ]
    }
    return markers(node.children)
  })
}

describe('RichText', () => {
  it('renders paragraphs with inline formatting and soft breaks', () => {
    expect(
      layout(
        '<p>Hello <strong>bold <em>bold italic</em></strong> <u>under</u> <s>gone</s></p><p>line one<br>line two</p><p></p><p>end</p>'
      )
    ).toMatchInlineSnapshot(`
      "Hello bold bold italic under gone
      line one
      line two
      ␣
      end"
    `)
  })

  it('renders bullet lists with nesting and multi-paragraph items', () => {
    expect(
      layout(
        '<ul><li><p>first</p><p>second paragraph</p></li><li><p>second</p><ul><li><p>nested</p><ul><li><p>deep</p></li></ul></li></ul></li></ul><p>after</p>'
      )
    ).toMatchInlineSnapshot(`
      "•    first
           second paragraph
      •    second
           ◦    nested
                ▪    deep
      after"
    `)
  })

  it('numbers ordered lists from their start attribute', () => {
    expect(
      layout(
        '<ol start="8"><li><p>eight</p></li><li><p>nine</p></li><li><p>ten</p><ol><li><p>nested restarts</p></li></ol></li></ol>'
      )
    ).toMatchInlineSnapshot(`
      "8.   eight
      9.   nine
      10.  ten
           1.   nested restarts"
    `)
  })

  it('supports alphabetic and roman numbering and explicit item values', () => {
    expect(
      layout(
        '<ol type="a"><li><p>a</p></li><li value="27"><p>aa</p></li></ol><ol type="I" start="4"><li><p>four</p></li><li><p>five</p></li></ol><ol type="i" start="9"><li><p>nine</p></li></ol>'
      )
    ).toMatchInlineSnapshot(`
      "a.   a
      aa.  aa
      IV.  four
      V.   five
      ix.  nine"
    `)
  })

  it('keeps formatting and soft breaks inside list items', () => {
    expect(layout('<ul><li><p><strong>bold</strong> text<br>continued</p></li></ul>'))
      .toMatchInlineSnapshot(`
      "•    bold text
           continued"
    `)
  })

  it('renders list items without paragraphs and empty items', () => {
    expect(layout('<ul><li>bare text</li><li></li><li><p></p></li></ul>'))
      .toMatchInlineSnapshot(`
        "•    bare text
        •    ␣
        •    ␣"
      `)
  })

  it('ignores whitespace between blocks and renders unknown tags transparently', () => {
    expect(
      layout(
        '<div>\n  <p>in div</p>\n  <ul>\n    <li><p><a href="#">link</a></p></li>\n  </ul>\n</div>'
      )
    ).toMatchInlineSnapshot(`
      "in div
      •    link"
    `)
  })

  it('widens the marker column for longer markers', () => {
    const [short, long, bullet] = [
      markers(render('<ol><li><p>a</p></li></ol>')),
      markers(render('<ol start="99"><li><p>a</p></li><li><p>b</p></li></ol>')),
      markers(render('<ul><li><p>a</p></li></ul>')),
    ]
    expect(short[0].width).toBe(15)
    expect(bullet[0].width).toBe(15)
    expect(long[0].width).toBeGreaterThan(short[0].width)
    expect(long[1].width).toBe(long[0].width)
  })

  it('scales the marker column with the font size', () => {
    const document = renderVueToPdfStructure({
      render: () =>
        h('Document', {}, [
          h('Page', {}, [
            h(RichText, { richText: '<ul><li><p>a</p></li></ul>', fontSize: 8 }),
          ]),
        ]),
    })
    expect(markers(document.children[0].children)[0].width).toBe(12)
  })
})
