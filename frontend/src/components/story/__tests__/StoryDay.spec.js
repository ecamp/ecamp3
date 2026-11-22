import { expect, it, vi } from 'vitest'
import { flushPromises, mount } from '@vue/test-utils'
import { setupVuetify } from '/tests/setupVuetify.js'
import StoryDay from '../StoryDay.vue'
import ApiRichtext from '@/components/form/api/ApiRichtext.vue'
import ApiWrapper from '@/components/form/api/ApiWrapper.vue'
import ERichtext from '@/components/form/base/ERichtext.vue'
import TiptapEditor from '@/components/form/tiptap/TiptapEditor.vue'

vi.mock('@/router.js', () => ({ scheduleEntryRoute: () => '/activity' }))

setupVuetify()

it('shows story images and preserves them when switching to edit mode', async () => {
  const src = `/api/images/${'a'.repeat(64)}/content`
  const patch = vi.fn().mockResolvedValue({})
  const chapter = {
    _meta: {
      self: '/content_node/single_texts/1',
      loading: false,
      load: Promise.resolve(),
    },
    data: { html: `<p>Story<img src="${src}" alt="Map" width="256"></p>` },
    root: () => ({ _meta: { self: '/content_nodes/1' } }),
  }
  const day = {
    _meta: { self: '/days/1' },
    start: '2026-07-01T00:00:00Z',
    period: () => ({
      camp: () => ({ id: '123456789abc' }),
      scheduleEntries: () => ({
        items: [
          {
            _meta: { self: '/schedule_entries/1' },
            day: () => day,
            activity: () => ({
              title: 'Treasure hunt',
              rootContentNode: chapter.root,
            }),
          },
        ],
      }),
    }),
  }
  const wrapper = mount(StoryDay, {
    props: { day, periodStoryChapters: [chapter] },
    global: {
      components: { ApiRichtext, ERichtext },
      mocks: { $t: (key) => key, api: { get: () => chapter, patch } },
      stubs: {
        VExpansionPanel: { template: '<div><slot /></div>' },
        VExpansionPanelTitle: { template: '<div><slot /></div>' },
        VExpansionPanelText: { template: '<div><slot /></div>' },
        CategoryChip: true,
        RouterLink: { template: '<a><slot /></a>' },
        BubbleMenu: true,
      },
    },
  })

  try {
    await flushPromises()
    expect(wrapper.get('img').attributes()).toMatchObject({
      src,
      alt: 'Map',
      width: '256',
    })
    expect(wrapper.find('input[type=file]').exists()).toBe(false)

    await wrapper.setProps({ editMode: true })
    expect(wrapper.get('img').attributes('src')).toBe(src)
    expect(wrapper.find('input[type=file]').exists()).toBe(true)
    const editor = wrapper.getComponent(TiptapEditor)
    editor.vm.editor.commands.insertContent('More story')
    expect(editor.emitted('tiptapUpdate').at(-1)[0]).toContain(src)
    const form = wrapper.getComponent(ApiWrapper)
    form.vm.debouncedSave.cancel()
    await form.vm.save()
    expect(patch).toHaveBeenCalledWith(chapter._meta.self, {
      data: { html: expect.stringContaining(src) },
    })
  } finally {
    wrapper.unmount()
  }
})
