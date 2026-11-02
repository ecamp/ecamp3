import { expect, it } from 'vitest'
import { h } from 'vue'
import RichText from '../campPrint/RichText.vue'
import { renderVueToPdfStructure } from '../renderer/vueRenderer.js'

it('renders formatted inline images outside PDF text nodes', () => {
  const src = '/api/camps/123456789abc/images/abcdef123456'
  const root = {
    render: () =>
      h('Document', {}, [
        h('Page', {}, [
          h(RichText, {
            richText: `<p>Before<strong><img src="${src}" width="320"></strong>After</p>`,
          }),
        ]),
      ]),
  }
  const result = renderVueToPdfStructure(root, {
    images: { [src]: 'data:image/png;base64,png' },
  })
  const nodes = []
  function visit(node, parent) {
    nodes.push({ node, parent })
    node.children?.forEach((child) => visit(child, node))
  }
  visit(result)
  const image = nodes.find(({ node }) => node.type === 'IMAGE')
  expect(image).toBeDefined()
  expect(image.parent.type).not.toBe('TEXT')
  expect(image.node.props.src).toBe('data:image/png;base64,png')
  const serialized = JSON.stringify(result, (key, value) =>
    key === 'parent' ? undefined : value
  )
  expect(serialized).toContain('Before')
  expect(serialized).toContain('After')
})
