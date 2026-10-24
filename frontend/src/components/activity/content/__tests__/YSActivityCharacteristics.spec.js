import { expect, it, vi } from 'vitest'
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

it('loads the selection and saves every option, including cleared options', async () => {
  const patch = vi.fn().mockResolvedValue({})
  const wrapper = shallowMount(YSActivityCharacteristics, {
    props: {
      contentNode: { data: { options }, $patch: patch },
      layoutMode: false,
    },
    global: { mocks: { $t: translate }, provide: { camp: {} } },
  })
  expect(wrapper.vm.localSelection).toEqual(['outdoorTechnique', 'natureAndEnvironment'])
  expect(wrapper.vm.items.map((item) => item.text)).toEqual([
    'Outdoor technique',
    'Community',
    'Nature and environment',
  ])
  wrapper.vm.onInput(['community'])
  wrapper.vm.debounceSave.flush()
  expect(patch).toHaveBeenLastCalledWith({
    data: {
      options: {
        outdoorTechnique: { checked: false },
        community: { checked: true },
        natureAndEnvironment: { checked: false },
      },
    },
  })
  wrapper.vm.onInput([])
  wrapper.vm.debounceSave.flush()
  expect(patch).toHaveBeenLastCalledWith({
    data: {
      options: {
        outdoorTechnique: { checked: false },
        community: { checked: false },
        natureAndEnvironment: { checked: false },
      },
    },
  })
  wrapper.unmount()
})
