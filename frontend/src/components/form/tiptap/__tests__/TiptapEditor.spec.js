import { describe, expect, it, vi } from 'vitest'
import { mount, flushPromises } from '@vue/test-utils'
import axios from 'axios'
import { setupVuetify } from '/tests/setupVuetify.js'
import TiptapEditor from '@/components/form/tiptap/TiptapEditor.vue'

setupVuetify()

function mountEditor(props = {}, renderMenu = false) {
  return mount(TiptapEditor, {
    props: { withExtensions: true, ...props },
    global: {
      mocks: { $t: (key) => key },
      stubs: {
        BubbleMenu: {
          name: 'BubbleMenu',
          template: renderMenu
            ? '<div class="bubble-menu"><slot /></div>'
            : '<div class="bubble-menu" />',
        },
      },
    },
  })
}

function hasBubbleMenu(wrapper) {
  return wrapper.findComponent({ name: 'BubbleMenu' }).exists()
}

describe('TiptapEditor', () => {
  const imageContent =
    '<p><img src="/api/camps/123456789abc/images/abcdef123456" alt="Tent" width="256"></p>'

  it('keeps the image selection and menu when the server normalizes HTML', async () => {
    const wrapper = mountEditor(
      { imageCampUri: '/camps/123456789abc', modelValue: imageContent },
      true
    )
    try {
      const menu = wrapper.findComponent({ name: 'BubbleMenu' }).vm
      wrapper.vm.editor.commands.setNodeSelection(1)
      await wrapper.setProps({
        modelValue: imageContent.replace('width="256">', 'width="256" />'),
      })
      expect(wrapper.vm.editor.isActive('image')).toBe(true)
      await vi.waitFor(() =>
        expect(
          wrapper
            .find('.bubble-menu')
            .findAllComponents({ name: 'TiptapToolbarButton' })
            .map((button) => button.props('icon'))
        ).toEqual(['mdi-plus', 'mdi-minus', 'mdi-pencil'])
      )
      expect(wrapper.findComponent({ name: 'BubbleMenu' }).vm).toBe(menu)
    } finally {
      wrapper.unmount()
    }
  })

  it('shows image actions instead of text formatting for an image selection', async () => {
    const wrapper = mountEditor(
      { imageCampUri: '/camps/123456789abc', modelValue: imageContent },
      true
    )
    try {
      wrapper.vm.editor.commands.setNodeSelection(1)
      await vi.waitFor(() => {
        const icons = wrapper
          .find('.bubble-menu')
          .findAllComponents({ name: 'TiptapToolbarButton' })
          .map((button) => button.props('icon'))
        expect(icons).toEqual(['mdi-plus', 'mdi-minus', 'mdi-pencil'])
      })
      expect(wrapper.find('input[type=number]').exists()).toBe(false)
    } finally {
      wrapper.unmount()
    }
  })

  it('changes image width in steps while preserving its attributes', () => {
    const wrapper = mountEditor({
      imageCampUri: '/camps/123456789abc',
      modelValue: imageContent,
    })
    wrapper.vm.editor.commands.setNodeSelection(1)
    wrapper.vm.resizeImage(32)
    expect(wrapper.vm.editor.getAttributes('image')).toMatchObject({
      width: 288,
      alt: 'Tent',
    })
    wrapper.vm.resizeImage(-32)
    expect(wrapper.vm.editor.getAttributes('image').width).toBe(256)
    wrapper.vm.resizeImage(-1000)
    expect(wrapper.vm.editor.getAttributes('image').width).toBe(32)
    wrapper.unmount()
  })

  it('saves alt text to the original image even after selection changes', async () => {
    const wrapper = mountEditor({
      imageCampUri: '/camps/123456789abc',
      modelValue: imageContent,
    })
    try {
      wrapper.vm.editor.commands.setNodeSelection(1)
      wrapper.vm.openImageAltDialog()
      expect(wrapper.vm.imageAltDraft).toBe('Tent')
      wrapper.vm.imageAltDraft = 'Camp tent'
      wrapper.vm.editor.commands.setTextSelection(2)
      wrapper.vm.saveImageAlt()
      expect(wrapper.vm.editor.getHTML()).toContain('alt="Camp tent"')
      expect(wrapper.vm.imageAltDialog).toBe(false)
    } finally {
      wrapper.unmount()
    }
  })

  it('discards cancelled alt text edits', () => {
    const wrapper = mountEditor({
      imageCampUri: '/camps/123456789abc',
      modelValue: imageContent,
    })
    wrapper.vm.editor.commands.setNodeSelection(1)
    wrapper.vm.openImageAltDialog()
    wrapper.vm.imageAltDraft = 'Discarded'
    wrapper.vm.closeImageAltDialog()
    expect(wrapper.vm.editor.getHTML()).toContain('alt="Tent"')
    wrapper.unmount()
  })
  it('emits the current document even when the computed HTML was cached', () => {
    const wrapper = mountEditor({ modelValue: '<p>Before</p>' })
    expect(wrapper.vm.html).toBe('<p>Before</p>')
    wrapper.vm.editor.commands.setContent('<p>After</p>')
    expect(wrapper.emitted('tiptapUpdate').at(-1)).toEqual(['<p>After</p>'])
    wrapper.unmount()
  })
  it('enables image uploads only for an explicitly configured editor', () => {
    const ordinary = mountEditor()
    const images = mountEditor({ imageCampUri: '/camps/123456789abc' })
    expect(ordinary.find('input[type=file]').exists()).toBe(false)
    expect(images.find('input[type=file]').exists()).toBe(true)
    ordinary.unmount()
    images.unmount()
  })

  it('uploads pasted files and inserts the returned camp image', async () => {
    const src = '/api/camps/123456789abc/images/abcdef123456'
    const post = vi.spyOn(axios, 'post').mockResolvedValue({ data: { src, width: 640 } })
    const wrapper = mountEditor({ imageCampUri: '/camps/123456789abc' })
    const preventDefault = vi.fn()
    try {
      expect(
        wrapper.vm.onImagePaste({
          clipboardData: {
            files: [new File(['image'], 'image.png', { type: 'image/png' })],
          },
          preventDefault,
        })
      ).toBe(true)
      await flushPromises()
      expect(preventDefault).toHaveBeenCalled()
      expect(post).toHaveBeenCalledWith(
        '/camps/123456789abc/images',
        expect.any(FormData),
        expect.objectContaining({
          withCredentials: true,
          headers: { Accept: 'application/json' },
        })
      )
      expect(wrapper.vm.editor.getHTML()).toContain(`src="${src}"`)
      expect(wrapper.emitted('tiptapUpdate').at(-1)[0]).toContain(src)
    } finally {
      post.mockRestore()
      wrapper.unmount()
    }
  })

  it('rejects unsupported files without starting an upload', async () => {
    const post = vi.spyOn(axios, 'post')
    const wrapper = mountEditor({ imageCampUri: '/camps/123456789abc' })
    try {
      await wrapper.vm.uploadImages(
        [new File(['svg'], 'image.svg', { type: 'image/svg+xml' })],
        1
      )
      expect(post).not.toHaveBeenCalled()
      expect(wrapper.vm.imageError).toBe('richTextImages.invalid')
    } finally {
      post.mockRestore()
      wrapper.unmount()
    }
  })
  it('passes autofocus on to tiptap, which focuses once the editor is attached', () => {
    expect(mountEditor({ autofocus: 'end' }).vm.editor.options.autofocus).toBe('end')
  })

  it('does not autofocus by default', () => {
    expect(mountEditor().vm.editor.options.autofocus).toBe(false)
  })

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
