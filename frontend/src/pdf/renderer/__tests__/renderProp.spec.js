import { describe, expect, it } from 'vitest'
import FontStore from '@react-pdf/font'
import layoutDocument from '@react-pdf/layout'
import { renderVueToPdfStructure } from '../vueRenderer.js'
import DynamicDocument from './DynamicDocument.vue'

const textsOf = (node, acc = []) => {
  if (node?.type === 'TEXT_INSTANCE') acc.push(node.value)
  ;(node?.children || []).forEach((child) => textsOf(child, acc))
  return acc
}

const typelessNodes = (node, acc = []) => {
  if (node && !node.type) acc.push(node)
  ;(node?.children || []).forEach((child) => typelessNodes(child, acc))
  return acc
}

describe('render props', () => {
  it('render their return value as text, whether string or number', async () => {
    const structure = renderVueToPdfStructure(DynamicDocument)

    const layout = await layoutDocument(structure, new FontStore())

    expect(textsOf(layout)).toEqual(expect.arrayContaining(['1 / 1', '1']))
    expect(typelessNodes(layout)).toEqual([])
  })
})
