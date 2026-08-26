import { describe, expect, it } from 'vitest'
import { mount } from '@vue/test-utils'
import { setupVuetify } from '/tests/setupVuetify.js'
import TiptapEditor from '@/components/form/tiptap/TiptapEditor.vue'

setupVuetify()

function mountEditor(props = {}) {
  return mount(TiptapEditor, {
    props: { withExtensions: true, ...props },
    global: {
      mocks: { $t: (key) => key },
      stubs: {
        BubbleMenu: { name: 'BubbleMenu', template: '<div class="bubble-menu" />' },
      },
    },
  })
}

function hasBubbleMenu(wrapper) {
  return wrapper.findComponent({ name: 'BubbleMenu' }).exists()
}

describe('TiptapEditor', () => {
  it('creates no bubble menu without the extensions', () => {
    expect(hasBubbleMenu(mountEditor({ withExtensions: false }))).toBe(false)
  })

  it('creates no bubble menu for a read-only editor', () => {
    expect(hasBubbleMenu(mountEditor({ editable: false }))).toBe(false)
  })

  it('keeps the bubble menu while the editor is temporarily not editable', async () => {
    const wrapper = mountEditor({ editable: true })
    expect(hasBubbleMenu(wrapper)).toBe(true)

    await wrapper.setProps({ editable: false })

    expect(hasBubbleMenu(wrapper)).toBe(true)
  })

  it('creates the bubble menu once a read-only editor becomes editable', async () => {
    const wrapper = mountEditor({ editable: false })

    await wrapper.setProps({ editable: true })

    expect(hasBubbleMenu(wrapper)).toBe(true)
  })

  it('follows the editable prop on the underlying editor', async () => {
    const wrapper = mountEditor({ editable: true })
    expect(wrapper.vm.editor.isEditable).toBe(true)

    await wrapper.setProps({ editable: false })

    expect(wrapper.vm.editor.isEditable).toBe(false)
  })
})
