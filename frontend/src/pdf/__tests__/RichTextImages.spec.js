import { expect, it } from 'vitest'
import { h } from 'vue'
import RichText from '../campPrint/RichText.vue'
import Storyboard from '../campPrint/scheduleEntry/contentNode/Storyboard.vue'
import { renderVueToPdfStructure } from '../renderer/vueRenderer.js'

it('renders formatted inline images outside PDF text nodes', () => {
  const src = `/api/images/${'abcdef0123456789'.repeat(4)}/content`
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

it('renders an image-only storyboard section in the program column', () => {
  const src = `/api/images/${'a'.repeat(64)}/content`
  const contentNode = {
    contentTypeName: 'Storyboard',
    data: {
      sections: {
        first: {
          column1: '',
          column2Html: `<p><img src="${src}" alt="Map" width="320"></p>`,
          column3: '',
          position: 0,
        },
      },
    },
  }
  const result = renderVueToPdfStructure(
    {
      render: () => h('Document', {}, [h('Page', {}, [h(Storyboard, { contentNode })])]),
    },
    { images: { [src]: 'data:image/png;base64,png' }, $tc: (key) => key }
  )
  const programImages = []
  function visit(node, inProgram = false) {
    inProgram ||= node.props?.class?.includes('storyboard-column-2') ?? false
    if (inProgram && node.type === 'IMAGE') programImages.push(node)
    node.children?.forEach((child) => visit(child, inProgram))
  }
  visit(result)
  expect(programImages).toHaveLength(1)
  expect(programImages[0].props.src).toBe('data:image/png;base64,png')
})
