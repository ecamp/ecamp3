import { expect, it } from 'vitest'
import { shallowMount } from '@vue/test-utils'
import YSActivityCharacteristics from '../YSActivityCharacteristics.vue'

const options = {
  outdoorTechnique: { checked: true },
  community: { checked: false },
  natureAndEnvironment: { checked: true },
}
const translations = {
  'contentNode.ysActivityCharacteristics.entity.option.outdoorTechnique.name':
    'Outdoor technique',
  'contentNode.ysActivityCharacteristics.entity.option.community.name': 'Community',
  'contentNode.ysActivityCharacteristics.entity.option.natureAndEnvironment.name':
    'Nature and environment',
}
const translate = (key) => translations[key]

it('renders only selected forms in the client PDF', () => {
  const wrapper = shallowMount(YSActivityCharacteristics, {
    props: { contentNode: { data: { options } } },
    global: {
      mocks: { $tc: translate },
      stubs: {
        View: { template: '<div><slot /></div>' },
        Text: { template: '<span><slot /></span>' },
      },
    },
  })
  expect(wrapper.text()).toContain('Outdoor technique')
  expect(wrapper.text()).toContain('Nature and environment')
  expect(wrapper.text()).not.toContain('Community')
})
