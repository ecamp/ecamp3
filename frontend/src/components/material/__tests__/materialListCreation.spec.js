import { afterEach, describe, expect, it, vi } from 'vitest'
import { flushPromises, mount } from '@vue/test-utils'
import { setupVuetify } from '/tests/setupVuetify.js'
import MaterialCreateItem from '../MaterialCreateItem.vue'
import MaterialListSelect from '../MaterialListSelect.vue'
import DialogMaterialItemCreate from '../DialogMaterialItemCreate.vue'
import DialogMaterialItemEdit from '../DialogMaterialItemEdit.vue'
import DialogForm from '@/components/dialog/DialogForm.vue'
import EForm from '@/components/form/base/EForm.vue'

setupVuetify()

let wrapper

afterEach(() => {
  wrapper?.unmount()
})

const camp = { _meta: { self: '/camps/1' } }
const list = { _meta: { self: '/material_lists/1' } }
list._meta.load = Promise.resolve(list)
const newListUri = '/material_lists/new'

const stubs = {
  EForm,
  ETextField: true,
  ENumberField: {
    template: '<div />',
    methods: { focus: vi.fn() },
  },
  MaterialListSelect: {
    name: 'MaterialListSelect',
    template: '<div />',
    emits: ['update:modelValue', 'update:creating'],
  },
  DialogForm: {
    props: ['submitEnabled', 'submitAction', 'modelValue'],
    template: '<div><slot /></div>',
  },
  PromptEntityDelete: true,
}

async function setCreating(creating) {
  wrapper.findComponent(MaterialListSelect).vm.$emit('update:creating', creating)
  await flushPromises()
}

function selectCreatedList() {
  wrapper.findComponent(MaterialListSelect).vm.$emit('update:modelValue', newListUri)
}

describe('material item submission while creating a material list', () => {
  it('blocks the table row until the newly created list has been selected', async () => {
    wrapper = mount(MaterialCreateItem, {
      props: { camp, materialList: list, columns: 6 },
      global: { stubs, mocks: { $t: (key) => key } },
    })
    await wrapper.setData({
      materialItem: { article: 'Seil', materialList: list._meta.self },
    })

    await setCreating(true)
    expect(wrapper.find('button').element.disabled).toBe(true)

    wrapper.vm.createMaterialItem(vi.fn())

    expect(wrapper.emitted('itemAdding')).toBeUndefined()
    expect(wrapper.vm.materialItem.article).toBe('Seil')
    expect(wrapper.vm.materialItem.materialList).toBe(list._meta.self)

    selectCreatedList()
    await setCreating(false)
    expect(wrapper.find('button').element.disabled).toBe(false)
    await wrapper.find('button').trigger('click')
    await flushPromises()

    expect(wrapper.emitted('itemAdding')).toHaveLength(1)
    expect(wrapper.emitted('itemAdding')[0][1]).toEqual({
      article: 'Seil',
      materialList: newListUri,
    })
  })

  it.each([
    ['creation', DialogMaterialItemCreate],
    ['editing', DialogMaterialItemEdit],
  ])('blocks the %s dialog until the new list is selected', async (_, component) => {
    const materialItem = {
      _meta: { self: '/material_items/1' },
      camp: () => camp,
      materialList: () => list,
      article: 'Seil',
      quantity: null,
      unit: '',
    }
    materialItem._meta.load = Promise.resolve(materialItem)
    const api = {
      get: vi.fn().mockReturnValue(materialItem),
      patch: vi.fn().mockResolvedValue(materialItem),
    }
    wrapper = mount(component, {
      props:
        component === DialogMaterialItemCreate
          ? {
              camp,
              materialList: list,
              materialItemCollection: { _meta: { self: '/material_items' } },
            }
          : { materialItemUri: materialItem._meta.self },
      global: { stubs, mocks: { $t: (key) => key, api } },
    })
    wrapper.vm.open()
    await flushPromises()
    await setCreating(true)
    const dialog = wrapper.findComponent(DialogForm)

    expect(dialog.props('submitEnabled')).toBe(false)
    await dialog.props('submitAction')()

    expect(api.patch).not.toHaveBeenCalled()
    expect(wrapper.emitted('itemAdding')).toBeUndefined()
    expect(wrapper.vm.showDialog).toBe(true)

    selectCreatedList()
    await setCreating(false)
    expect(dialog.props('submitEnabled')).toBe(true)
    await dialog.props('submitAction')()
    await flushPromises()

    if (component === DialogMaterialItemCreate) {
      expect(wrapper.emitted('itemAdding')).toHaveLength(1)
      expect(wrapper.emitted('itemAdding')[0][1].materialList).toBe(newListUri)
    } else {
      expect(api.patch).toHaveBeenCalledWith(materialItem._meta.self, {
        article: 'Seil',
        quantity: null,
        unit: '',
        materialList: newListUri,
      })
    }
    expect(wrapper.vm.showDialog).toBe(false)
  })
})
