import { shallowMount } from '@vue/test-utils'
import MaterialTable from '../MaterialTable.vue'
import PromptEntityDelete from '@/components/prompt/PromptEntityDelete.vue'
import DialogMaterialItemEdit from '../DialogMaterialItemEdit.vue'

const mount = (materialList) => {
  const reload = materialList?.().$reload ?? vi.fn().mockResolvedValue({})
  const wrapper = shallowMount(MaterialTable, {
    props: {
      camp: { materialLists: () => ({ items: [], _meta: {} }) },
      materialItemCollection: {
        _meta: {},
        items: [
          {
            id: 'item',
            _meta: { self: '/material_items/item' },
            materialList,
            article: 'Rope',
          },
        ],
      },
    },
    global: {
      provide: { isContributor: true },
      mocks: { api: { reload }, $t: (key) => key },
      directives: { resizeobserver: {} },
      stubs: {
        PromptEntityDelete: false,
        VDataTable: {
          props: ['items'],
          template:
            '<div><slot v-if="items.length" name="item.lastColumn" :item="items[0]" /></div>',
        },
      },
    },
  })
  return { wrapper, reload }
}

it.each([true, false])(
  'reloads the list count after successful deletion (desktop: %s)',
  async (desktop) => {
    const list = {
      name: 'Equipment',
      itemCount: 1,
      $reload: vi.fn().mockResolvedValue({}),
    }
    const { wrapper, reload } = mount(() => list)
    await wrapper.setData({ clientWidth: desktop ? 1000 : 500 })

    expect(reload).not.toHaveBeenCalled()
    const onDeleted = wrapper
      .findComponent(desktop ? PromptEntityDelete : DialogMaterialItemEdit)
      .props(desktop ? 'successHandler' : 'deleteSuccessHandler')

    // The API removes the row before the deletion promise resolves.
    await wrapper.setProps({ materialItemCollection: { _meta: {}, items: [] } })
    expect(wrapper.findComponent(PromptEntityDelete).exists()).toBe(false)
    expect(wrapper.findComponent(DialogMaterialItemEdit).exists()).toBe(false)
    onDeleted()

    expect(reload).toHaveBeenCalledExactlyOnceWith()
    wrapper.unmount()
  }
)

it('does not reload a list when deleting an unassigned item', async () => {
  const { wrapper, reload } = mount(null)
  await wrapper.setData({ clientWidth: 1000 })

  wrapper.findComponent(PromptEntityDelete).props('successHandler')()

  expect(reload).not.toHaveBeenCalled()
  wrapper.unmount()
})

it('runs the delete success handler even after the prompt is unmounted', async () => {
  let resolveDelete
  const successHandler = vi.fn()
  const wrapper = shallowMount(PromptEntityDelete, {
    props: { entity: '/material_items/item', successHandler },
    global: {
      mocks: {
        $t: (key) => key,
        api: {
          del: () => new Promise((resolve) => (resolveDelete = resolve)),
        },
      },
    },
  })
  const deletion = wrapper.vm.del()
  wrapper.unmount()
  resolveDelete()
  await deletion

  expect(successHandler).toHaveBeenCalledExactlyOnceWith(undefined)
})

it('passes the mobile delete callback directly to the delete prompt', () => {
  const deleteSuccessHandler = vi.fn()
  const wrapper = shallowMount(DialogMaterialItemEdit, {
    props: { materialItemUri: '/material_items/item', deleteSuccessHandler },
    global: {
      mocks: {
        $t: (key) => key,
        api: {
          get: () => ({
            article: 'Rope',
            camp: () => ({ materialLists: () => ({}) }),
          }),
        },
      },
      stubs: {
        PromptEntityDelete: false,
        DialogForm: {
          template: '<div><slot name="moreActions" /></div>',
        },
      },
    },
  })

  expect(wrapper.findComponent(PromptEntityDelete).props('successHandler')).toBe(
    deleteSuccessHandler
  )
  wrapper.unmount()
})
