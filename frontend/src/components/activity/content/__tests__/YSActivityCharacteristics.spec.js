import { describe, expect, it, vi } from 'vitest'
import { shallowMount } from '@vue/test-utils'
import YSActivityCharacteristics from '../YSActivityCharacteristics.vue'
import PdfCharacteristics from '@/pdf/campPrint/scheduleEntry/contentNode/YSActivityCharacteristics.vue'
import de from '../../../../../../common/locales/de.json'

const options = {
  outdoorTechnique: { checked: true },
  community: { checked: false },
  natureAndEnvironment: { checked: true },
}
const translate = (key) => key.split('.').reduce((value, part) => value[part], de)

describe('YSActivityCharacteristics', () => {
  it('loads the selection and saves every option, including cleared options', async () => {
    const patch = vi.fn().mockResolvedValue({})
    const wrapper = shallowMount(YSActivityCharacteristics, {
      props: {
        contentNode: { data: { options }, $patch: patch },
        layoutMode: false,
      },
      global: { mocks: { $t: translate }, provide: { camp: {} } },
    })
    expect(wrapper.vm.localSelection).toEqual([
      'outdoorTechnique',
      'natureAndEnvironment',
    ])
    expect(wrapper.vm.items.map((item) => item.text)).toEqual([
      'Outdoor- und Lagerbautechniken wirksam anwenden',
      'Die Gemeinschaft wohlwollend stärken',
      'Respektvoll mit Natur und Umwelt umgehen',
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

  it('renders only selected forms in the client PDF', () => {
    const wrapper = shallowMount(PdfCharacteristics, {
      props: { contentNode: { data: { options } } },
      global: {
        mocks: { $tc: translate },
        stubs: {
          View: { template: '<div><slot /></div>' },
          Text: { template: '<span><slot /></span>' },
        },
      },
    })
    expect(wrapper.text()).toContain('Outdoor- und Lagerbautechniken wirksam anwenden')
    expect(wrapper.text()).toContain('Respektvoll mit Natur und Umwelt umgehen')
    expect(wrapper.text()).not.toContain('Die Gemeinschaft wohlwollend stärken')
  })
})
