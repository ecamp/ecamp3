import { afterAll, beforeAll, describe, expect, it, vi } from 'vitest'
import { mount, shallowMount } from '@vue/test-utils'
import { nextTick } from 'vue'
import { setupVuetify } from '/tests/setupVuetify.js'
import MultiSelect from '../MultiSelect.vue'
import YSActivityCharacteristics from '../YSActivityCharacteristics.vue'
import LAThematicArea from '../LAThematicArea.vue'
import ESelect from '@/components/form/base/ESelect.vue'

setupVuetify()

// jsdom does not implement the viewport API used to position Vuetify menus.
beforeAll(() => {
  vi.stubGlobal(
    'visualViewport',
    Object.assign(new EventTarget(), {
      width: 1024,
      height: 768,
      offsetLeft: 0,
      offsetTop: 0,
    })
  )
})
afterAll(() => vi.unstubAllGlobals())

const items = [
  { value: 'first', text: 'A long title that can wrap onto a second line' },
  { value: 'second', text: 'Second option', description: 'The second description' },
  { value: 'third', text: 'Third option', description: 'The third description' },
  { value: 'fourth', text: 'Fourth option', description: 'The fourth description' },
]

function mountSelect(modelValue = [], props = {}) {
  return mount(MultiSelect, {
    props: {
      items,
      contentNode: {
        data: {
          options: Object.fromEntries(
            items.map((item) => [
              item.value,
              { checked: modelValue.includes(item.value) },
            ])
          ),
        },
        $patch: vi.fn().mockResolvedValue({}),
      },
      layoutMode: false,
      placeholder: 'Choose options',
      ...props,
    },
    global: {
      provide: { camp: {} },
      stubs: { ContentNodeCard: { template: '<div><slot /></div>' } },
    },
    attachTo: document.body,
  })
}

describe('MultiSelect', () => {
  it('flushes pending changes when the content node is unmounted', () => {
    const wrapper = mountSelect(['first'])
    const patch = wrapper.props('contentNode').$patch
    wrapper.vm.onInput(['second'])
    expect(patch).not.toHaveBeenCalled()

    wrapper.unmount()
    expect(patch).toHaveBeenCalledExactlyOnceWith({
      data: {
        options: {
          first: { checked: false },
          second: { checked: true },
          third: { checked: false },
          fourth: { checked: false },
        },
      },
    })
  })

  it('renders titles without subtitles as one-line items in option order', () => {
    const wrapper = mountSelect(['third', 'first'])
    const selection = wrapper.find('.v-select__selection')

    expect(selection.findAll('.v-list-item-title').map((title) => title.text())).toEqual([
      items[0].text,
      items[2].text,
    ])
    expect(selection.findAll('.v-list-item')[0].classes()).toContain(
      'v-list-item--one-line'
    )
    expect(
      selection.findAll('.v-list-item')[0].find('.v-list-item-subtitle').exists()
    ).toBe(false)
    expect(selection.text()).toContain(items[2].description)
    wrapper.unmount()
  })

  it('uses three lines for descriptions with few selections and two with more', async () => {
    const wrapper = mountSelect(['second'])
    expect(wrapper.find('.v-select__selection .v-list-item').classes()).toContain(
      'v-list-item--three-line'
    )

    await wrapper.setData({ localSelection: items.map((item) => item.value) })
    const selection = wrapper.find('.v-select__selection')
    expect(selection.findAll('.v-list-item')[1].classes()).toContain(
      'v-list-item--two-line'
    )
    expect(selection.findAll('.v-list-item-subtitle')).toHaveLength(3)
    wrapper.unmount()
  })

  it('opens from a selected title and handles selection and deselection changes', async () => {
    const wrapper = mountSelect(['first'])
    await wrapper.find('.v-select__selection .v-list-item-title').trigger('mousedown')
    await nextTick()

    const options = [...document.querySelectorAll('[role="option"]')]
    expect(options).toHaveLength(items.length)
    expect(options[0].classList.contains('v-list-item--one-line')).toBe(true)
    expect(options[1].classList.contains('v-list-item--three-line')).toBe(true)
    expect(options[1].textContent).toContain(items[1].description)

    options[1].click()
    expect(wrapper.vm.localSelection).toEqual(['first', 'second'])
    await nextTick()
    options[0].click()
    expect(wrapper.vm.localSelection).toEqual(['second'])
    wrapper.unmount()
  })

  it('forwards the placeholder and disabled state', async () => {
    const wrapper = mountSelect([], { disabled: true })
    expect(wrapper.find('input').attributes('placeholder')).toBe('Choose options')
    expect(wrapper.find('input').element.disabled).toBe(true)
    await wrapper.find('.v-field').trigger('mousedown')
    expect(document.querySelector('[role="option"]')).toBeNull()
    wrapper.unmount()
  })
})

describe('YSActivityCharacteristics', () => {
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
    const parent = shallowMount(YSActivityCharacteristics, {
      props: {
        contentNode: { data: { options }, $patch: patch },
        layoutMode: false,
      },
      global: {
        mocks: { $t: translate },
        provide: { camp: {} },
        stubs: { MultiSelect: false },
      },
    })
    const wrapper = parent.findComponent(MultiSelect)
    expect(wrapper.vm.localSelection).toEqual([
      'outdoorTechnique',
      'natureAndEnvironment',
    ])
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
    parent.unmount()
  })
})

describe('LAThematicArea', () => {
  it('passes titles and descriptions to the multiselect and saves its changes', async () => {
    const patch = vi.fn().mockResolvedValue({})
    const wrapper = shallowMount(LAThematicArea, {
      props: {
        contentNode: {
          data: {
            options: {
              outdoorTechnique: { checked: true },
              community: { checked: false },
            },
          },
          $patch: patch,
        },
        layoutMode: false,
      },
      global: {
        mocks: { $t: (key) => key },
        provide: { camp: {} },
        stubs: {
          MultiSelect: false,
          ContentNodeCard: { template: '<div><slot /></div>' },
        },
      },
    })
    const node = wrapper.findComponent(MultiSelect)
    const select = wrapper.findComponent(ESelect)
    expect(node.vm.localSelection).toEqual(['outdoorTechnique'])
    expect(node.props('items')[0]).toMatchObject({
      text: 'contentNode.laThematicArea.entity.option.outdoorTechnique.name',
      description:
        'contentNode.laThematicArea.entity.option.outdoorTechnique.description',
      value: 'outdoorTechnique',
    })

    select.vm.$emit('update:modelValue', ['community'])
    node.vm.debounceSave.flush()
    expect(patch).toHaveBeenCalledWith({
      data: {
        options: { outdoorTechnique: { checked: false }, community: { checked: true } },
      },
    })

    await wrapper.setProps({ layoutMode: true })
    expect(select.attributes('disabled')).toBe('true')
    wrapper.unmount()
  })
})
