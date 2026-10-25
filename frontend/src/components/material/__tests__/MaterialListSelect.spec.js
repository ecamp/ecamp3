import { afterEach, beforeEach, describe, expect, it, vi } from 'vitest'
import { flushPromises, mount } from '@vue/test-utils'
import { reactive } from 'vue'
import { Form, defineRule } from 'vee-validate'
import { required } from '@vee-validate/rules'
import { setupVuetify } from '/tests/setupVuetify.js'
import MaterialListSelect from '../MaterialListSelect.vue'

setupVuetify()
defineRule('required', required)

let wrapper

beforeEach(() => {
  vi.stubGlobal('visualViewport', null)
})

afterEach(() => {
  wrapper?.unmount()
  document.body.innerHTML = ''
  vi.unstubAllGlobals()
})

function mountSelect(items = [], fieldProps = {}) {
  const collection = reactive({ items: [...items] })
  const camp = {
    _meta: { self: '/camps/1' },
    materialLists: () => collection,
  }
  const createdList = { _meta: { self: '/material_lists/new' }, name: 'Werkzeug' }
  const api = {
    get: () => ({}),
    href: vi.fn().mockResolvedValue('/material_lists'),
    post: vi.fn().mockResolvedValue(createdList),
    reload: vi.fn().mockImplementation(async () => collection.items.push(createdList)),
  }

  wrapper = mount(
    {
      components: { Form, MaterialListSelect },
      data: () => ({ camp, selected: null, fieldProps }),
      template: `
        <Form ref="form" as="div">
          <MaterialListSelect v-model="selected" :camp="camp" v-bind="fieldProps"
            path="materialList" vee-rules="required" />
        </Form>
      `,
    },
    {
      attachTo: document.body,
      global: {
        mocks: { $t: (key) => key, api },
      },
    }
  )

  return { api, collection, createdList }
}

async function search(text) {
  const input = wrapper.find('input')
  await input.trigger('focus')
  await input.setValue(text)
  await flushPromises()
}

function createAction() {
  return [...document.querySelectorAll('.v-list-item')].find((item) =>
    item.textContent.includes('components.material.materialListSelect.createNew')
  )
}

describe('MaterialListSelect', () => {
  const items = [
    { _meta: { self: '/material_lists/1' }, name: 'Lagermaterial' },
    { _meta: { self: '/material_lists/2' }, name: 'Lebensmittel' },
  ]

  it.each(['material', 'LAGERMATERIAL', 'Lagermatrial', 'Lagermaetrial'])(
    'finds an existing list with the fuzzy search %s',
    async (query) => {
      mountSelect(items)

      await search(query)

      const options = [...document.querySelectorAll('[role="option"]')]
      expect(options.some((option) => option.textContent === 'Lagermaterial')).toBe(true)
      expect(options.some((option) => option.textContent === 'Lebensmittel')).toBe(false)
      expect(document.body.textContent).not.toContain(
        'components.material.materialListSelect.noListsFound'
      )
    }
  )

  it('selects an existing list by URI and satisfies required validation', async () => {
    mountSelect(items)
    await search('Lagermatrial')

    document.querySelector('[role="option"]').click()
    await flushPromises()

    expect(wrapper.vm.selected).toBe('/material_lists/1')
    expect(await wrapper.findComponent(Form).vm.validate()).toMatchObject({
      valid: true,
    })
  })

  it('shows a non-selectable notice above creation when no existing list matches', async () => {
    const { api } = mountSelect(items)
    await search('Werkzeug')

    const notice = [...document.querySelectorAll('.v-list-item')].find((item) =>
      item.textContent.includes('components.material.materialListSelect.noListsFound')
    )
    expect(notice).toBeDefined()
    expect(notice.classList.contains('v-list-item--link')).toBe(false)
    expect(notice.hasAttribute('tabindex')).toBe(false)
    expect(
      notice.compareDocumentPosition(createAction()) & Node.DOCUMENT_POSITION_FOLLOWING
    ).toBeTruthy()

    notice.click()
    await flushPromises()

    expect(api.post).not.toHaveBeenCalled()
    expect(wrapper.vm.selected).toBeNull()
  })

  it('offers the trimmed search as the title and creation as the subtitle without existing lists', async () => {
    mountSelect()
    await search('  Werkzeug  ')

    expect(createAction()).toBeDefined()
    expect(createAction().querySelector('.v-list-item-title').textContent).toBe(
      'Werkzeug'
    )
    expect(createAction().querySelector('.v-list-item-subtitle').textContent).toBe(
      'components.material.materialListSelect.createNew'
    )
    expect(wrapper.vm.selected).toBeNull()
  })

  it('reloads lists before selecting the created list and validating the item', async () => {
    const { api, collection, createdList } = mountSelect()
    let finishReload
    api.reload.mockImplementation(
      () =>
        new Promise((resolve) => {
          finishReload = () => {
            collection.items.push(createdList)
            resolve()
          }
        })
    )
    await search('Werkzeug')
    createAction().click()
    await flushPromises()

    expect(api.post).toHaveBeenCalledWith('/material_lists', {
      camp: '/camps/1',
      name: 'Werkzeug',
    })
    expect(api.reload).toHaveBeenCalledWith(collection)
    expect(wrapper.vm.selected).toBeNull()
    expect(wrapper.find('input').attributes('aria-expanded')).toBe('false')
    expect(wrapper.find('.v-progress-circular').exists()).toBe(true)
    expect(document.querySelector('[role="dialog"]')).toBeNull()

    finishReload()
    await flushPromises()

    expect(wrapper.vm.selected).toBe(createdList._meta.self)
    expect(wrapper.find('.v-autocomplete__selection-text').text()).toBe('Werkzeug')
    expect(wrapper.find('input').element.value).toBe('Werkzeug')
    expect(wrapper.find('input').attributes('aria-expanded')).toBe('false')
    expect(await wrapper.findComponent(Form).vm.validate()).toMatchObject({
      valid: true,
    })
    expect(wrapper.find('.v-progress-circular').exists()).toBe(false)
    expect(wrapper.findComponent(MaterialListSelect).emitted('update:creating')).toEqual([
      [true],
      [false],
    ])
  })

  it('shows the saved name while focused and retains the selection after refocusing', async () => {
    const { createdList } = mountSelect()
    await search('  Werkzeug  ')
    createAction().click()
    await flushPromises()

    expect(wrapper.find('input').element.value).toBe(createdList.name)
    expect(wrapper.find('input').attributes('aria-expanded')).toBe('false')

    await wrapper.find('input').trigger('blur')
    await flushPromises()
    expect(wrapper.vm.selected).toBe(createdList._meta.self)
    expect(wrapper.find('.v-autocomplete__selection-text').text()).toBe(createdList.name)

    await wrapper.find('input').trigger('focus')
    await flushPromises()
    expect(wrapper.find('input').element.value).toBe(createdList.name)
    expect(wrapper.vm.selected).toBe(createdList._meta.self)
  })

  it('keeps the selection and does not create a list when the menu is dismissed', async () => {
    const { api } = mountSelect(items)
    await wrapper.setData({ selected: '/material_lists/1' })
    await search('Werkzeug')
    await wrapper.find('input').trigger('keydown', { key: 'Escape' })
    await flushPromises()

    expect(api.post).not.toHaveBeenCalled()
    expect(wrapper.vm.selected).toBe('/material_lists/1')
  })

  it('shows the error below the field and keeps the search when saving fails', async () => {
    const { api } = mountSelect()
    api.post.mockRejectedValue(new Error('Saving failed'))
    await search('Werkzeug')
    createAction().click()
    await flushPromises()

    expect(wrapper.find('.v-messages').text()).toBe('Saving failed')
    expect(wrapper.find('input').element.value).toBe('Werkzeug')
    expect(wrapper.find('input').attributes('aria-expanded')).toBe('false')
    expect(api.reload).not.toHaveBeenCalled()
    expect(wrapper.vm.selected).toBeNull()
    expect(wrapper.find('.v-progress-circular').exists()).toBe(false)
  })

  it('does not offer creation for an empty name', async () => {
    const { api } = mountSelect()
    await search('   ')

    expect(createAction()).toBeUndefined()
    expect(api.post).not.toHaveBeenCalled()
  })

  it.each(['Lagermaterial', '  Lagermaterial  '])(
    'does not offer creation when the name already exists: %s',
    async (query) => {
      const { api } = mountSelect(items)
      await search(query)

      expect(createAction()).toBeUndefined()
      expect(api.post).not.toHaveBeenCalled()
    }
  )

  it('offers creation when only the letter case differs from an existing name', async () => {
    const { api } = mountSelect(items)
    await search('LAGERMATERIAL')

    expect(createAction()).toBeDefined()
    createAction().click()
    await flushPromises()

    expect(api.post).toHaveBeenCalledWith('/material_lists', {
      camp: '/camps/1',
      name: 'LAGERMATERIAL',
    })
  })

  it('closes the menu before posting and prevents duplicate submissions while saving', async () => {
    const { api, createdList } = mountSelect([], { readonly: false })
    let finishPost
    api.post.mockImplementation(() => {
      expect(wrapper.find('input').attributes('aria-expanded')).toBe('false')
      return new Promise((resolve) => {
        finishPost = () => resolve(createdList)
      })
    })
    await search('Werkzeug')
    const action = createAction()
    action.click()
    action.click()
    await flushPromises()

    expect(api.post).toHaveBeenCalledOnce()
    expect(wrapper.findComponent(MaterialListSelect).emitted('update:creating')).toEqual([
      [true],
    ])
    expect(wrapper.find('input').element.readOnly).toBe(true)
    expect(wrapper.find('.v-progress-circular').exists()).toBe(true)

    finishPost()
    await flushPromises()

    expect(wrapper.find('input').element.readOnly).toBe(false)
    expect(wrapper.vm.selected).toBe(createdList._meta.self)
  })

  it('combines inherited error messages with creation errors', async () => {
    const { api } = mountSelect([], {
      errorMessages: ['Existing error'],
      maxErrors: 2,
    })
    api.post.mockRejectedValue(new Error('Saving failed'))
    await search('Werkzeug')
    createAction().click()
    await flushPromises()

    expect(wrapper.find('.v-messages').text()).toContain('Existing error')
    expect(wrapper.find('.v-messages').text()).toContain('Saving failed')
    expect(wrapper.findComponent(MaterialListSelect).emitted('update:creating')).toEqual([
      [true],
      [false],
    ])
  })

  it('clears the error when retrying and selects the newly created list', async () => {
    const { api, createdList } = mountSelect()
    api.post.mockRejectedValueOnce(new Error('Saving failed'))
    await search('Werkzeug')
    createAction().click()
    await flushPromises()
    expect(wrapper.find('.v-messages').text()).toBe('Saving failed')

    await search('Werkzeug')
    createAction().click()
    await flushPromises()

    expect(api.post).toHaveBeenCalledTimes(2)
    expect(wrapper.find('.v-messages').exists()).toBe(false)
    expect(wrapper.vm.selected).toBe(createdList._meta.self)
  })

  it('retries reloading without creating the same list twice', async () => {
    const { api, createdList } = mountSelect()
    api.reload.mockRejectedValueOnce(new Error('Reload failed'))
    await search('Werkzeug')
    createAction().click()
    await flushPromises()
    expect(wrapper.find('.v-messages').text()).toBe('Reload failed')
    expect(wrapper.vm.selected).toBeNull()

    await search('Werkzeug')
    createAction().click()
    await flushPromises()

    expect(api.post).toHaveBeenCalledOnce()
    expect(api.reload).toHaveBeenCalledTimes(2)
    expect(wrapper.vm.selected).toBe(createdList._meta.self)
  })
})
