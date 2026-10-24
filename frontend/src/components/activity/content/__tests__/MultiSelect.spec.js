import { afterAll, beforeAll, describe, expect, it, vi } from 'vitest'
import { mount } from '@vue/test-utils'
import { nextTick } from 'vue'
import { setupVuetify } from '/tests/setupVuetify.js'
import MultiSelect from '../MultiSelect.vue'

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

  it('renders title and subtitle with three lines for descriptions', async () => {
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
