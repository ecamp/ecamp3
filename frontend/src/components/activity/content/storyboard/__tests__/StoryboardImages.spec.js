import { expect, it, vi } from 'vitest'
import { flushPromises, mount } from '@vue/test-utils'
import axios from 'axios'
import { setupVuetify } from '/tests/setupVuetify.js'
import StoryboardRowDefault from '../StoryboardRowDefault.vue'
import StoryboardRowDense from '../StoryboardRowDense.vue'
import ApiRichtext from '@/components/form/api/ApiRichtext.vue'
import ApiWrapper from '@/components/form/api/ApiWrapper.vue'
import ERichtext from '@/components/form/base/ERichtext.vue'
import TiptapEditor from '@/components/form/tiptap/TiptapEditor.vue'

setupVuetify()

it.each([StoryboardRowDefault, StoryboardRowDense])(
  'shows, uploads and saves program images in $name',
  async (component) => {
    const src = `/api/images/${'a'.repeat(64)}/content`
    const uploadedSrc = `/api/images/${'b'.repeat(64)}/content`
    const key = 'cb26d76a-9e3b-43f0-a7c4-0f2ad0ea8029'
    const entity = {
      _meta: {
        self: '/content_node/storyboards/1',
        loading: false,
        load: Promise.resolve(),
      },
      data: {
        sections: {
          [key]: { column2Html: `<p><img src="${src}" alt="Map" width="320"></p>` },
        },
      },
    }
    const patch = vi.fn().mockResolvedValue({})
    const post = vi
      .spyOn(axios, 'post')
      .mockResolvedValue({ data: { src: uploadedSrc, width: 640 } })
    const wrapper = mount(component, {
      props: { itemKey: key, layoutMode: false, isLastSection: true, disabled: true },
      global: {
        components: { ApiRichtext, ERichtext },
        provide: { apiUri: entity._meta.self, camp: { id: '123456789abc' } },
        mocks: { $t: (key) => key, api: { get: () => entity, patch } },
        stubs: { ApiTextField: true, BubbleMenu: true, DialogRemoveSection: true },
      },
    })

    try {
      await flushPromises()
      expect(wrapper.get('.ProseMirror img[src]').attributes()).toMatchObject({
        src,
        alt: 'Map',
        width: '320',
      })
      expect(wrapper.find('input[type=file]').exists()).toBe(false)

      await wrapper.setProps({ disabled: false })
      wrapper.getComponent(TiptapEditor).vm.chooseImages()
      const input = wrapper.get('input[type=file]')
      Object.defineProperty(input.element, 'files', {
        value: [new File(['image'], 'map.png', { type: 'image/png' })],
      })
      await input.trigger('change')
      await flushPromises()
      expect(post.mock.calls[0][1].get('campId')).toBe('123456789abc')
      expect(
        wrapper.findAll('.ProseMirror img[src]').map((image) => image.attributes('src'))
      ).toEqual(expect.arrayContaining([src, uploadedSrc]))

      const form = wrapper.getComponent(ApiWrapper)
      form.vm.debouncedSave.cancel()
      await form.vm.save()
      expect(patch).toHaveBeenCalledWith(entity._meta.self, {
        data: {
          sections: { [key]: { column2Html: expect.stringContaining(uploadedSrc) } },
        },
      })
    } finally {
      wrapper.unmount()
      post.mockRestore()
    }
  }
)
