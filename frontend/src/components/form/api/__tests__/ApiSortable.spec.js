import { expect, it, vi } from 'vitest'
import { mount } from '@vue/test-utils'
import { reactive } from 'vue'
import flushPromises from 'flush-promises'
import draggable from 'vuedraggable'
import ApiSortable from '../ApiSortable.vue'

it('removes all draggable rows when leaving sorting mode after moving an item', async () => {
  const first = { _meta: { self: '/progress_labels/first' }, position: 1 }
  const second = { _meta: { self: '/progress_labels/second' }, position: 2 }
  const endpoint = reactive({
    items: [first, second],
    $reload: vi.fn(async () => {
      endpoint.items = [second, first]
    }),
  })
  const patch = vi.fn().mockResolvedValue({})
  const wrapper = mount(
    {
      components: { ApiSortable },
      data: () => ({ sorting: true, endpoint }),
      template: `
        <div>
          <button @click="sorting = !sorting">Toggle sorting</button>
          <ApiSortable v-if="sorting" :endpoint="endpoint">
            <template #default="{ item }">
              <span class="drag-and-drop-handle">{{ item._meta.self }}</span>
            </template>
          </ApiSortable>
          <div v-else v-for="item in endpoint.items" :key="item._meta.self" class="editable">
            {{ item._meta.self }}
          </div>
        </div>
      `,
    },
    {
      global: {
        mocks: { api: { patch } },
        stubs: { 'transition-group': false },
      },
    }
  )
  const sortable = wrapper.findComponent(ApiSortable)
  const drag = wrapper.findComponent(draggable)
  const moved = wrapper.get('[data-href="/progress_labels/first"]').element
  const container = moved.parentElement
  const event = {
    item: moved,
    from: container,
    oldIndex: Array.from(container.children).indexOf(moved),
    newIndex: 1,
    newDraggableIndex: 1,
  }

  drag.vm.onDragStart(event)
  container.appendChild(moved)
  drag.vm.onDragUpdate(event)
  await sortable.vm.finishDrag(event)
  await flushPromises()

  expect(patch).toHaveBeenCalledExactlyOnceWith('/progress_labels/first', {
    position: 2,
  })
  expect(
    wrapper.findAll('[data-href]').map((row) => row.attributes('data-href'))
  ).toEqual(['/progress_labels/second', '/progress_labels/first'])

  await wrapper.setData({ sorting: false })
  await flushPromises()

  expect(wrapper.findAll('.drag-and-drop-handle')).toHaveLength(0)
  expect(wrapper.findAll('.editable')).toHaveLength(2)
  wrapper.unmount()
})
