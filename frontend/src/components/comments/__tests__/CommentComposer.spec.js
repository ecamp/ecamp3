import { beforeEach, describe, expect, it, vi } from 'vitest'
import { mount } from '@vue/test-utils'
import { setupVuetify } from '/tests/setupVuetify.js'
import CommentComposer from '@/components/comments/CommentComposer.vue'

setupVuetify()

const camp = { _meta: { self: '/camps/1' } }
const activity = { _meta: { self: '/activities/1' } }

let post

function mountComposer(props = {}) {
  post = vi.fn().mockResolvedValue({})
  return mount(CommentComposer, {
    props: { camp, ...props },
    global: {
      mocks: {
        $t: (key) => key,
        api: { get: () => ({}), href: () => Promise.resolve('/comments'), post },
      },
      stubs: {
        ERichtext: {
          name: 'ERichtext',
          props: ['modelValue'],
          template: '<div class="richtext"><slot name="append-inner" /></div>',
        },
      },
    },
  })
}

function send(wrapper) {
  return wrapper.find('[data-testid="comment-submit"]')
}

async function type(wrapper, textHtml) {
  wrapper.findComponent({ name: 'ERichtext' }).vm.$emit('update:modelValue', textHtml)
  await wrapper.vm.$nextTick()
}

describe('CommentComposer', () => {
  beforeEach(() => {
    post = undefined
  })

  it('has no full-width submit button taking away reading space', () => {
    expect(mountComposer().find('.v-btn--block').exists()).toBe(false)
  })

  it('offers the send button inside the input', () => {
    expect(
      mountComposer().find('.richtext [data-testid="comment-submit"]').exists()
    ).toBe(true)
  })

  it('disables sending while there is nothing to send', () => {
    expect(send(mountComposer()).attributes('disabled')).toBeDefined()
  })

  it('enables sending once something was typed', async () => {
    const wrapper = mountComposer()

    await type(wrapper, '<p>Hallo</p>')

    expect(send(wrapper).attributes('disabled')).toBeUndefined()
  })

  it('posts the comment on the camp when clicked', async () => {
    const wrapper = mountComposer()
    await type(wrapper, '<p>Hallo</p>')

    await send(wrapper).trigger('click')

    expect(post).toHaveBeenCalledWith('/comments', {
      textHtml: '<p>Hallo</p>',
      camp: '/camps/1',
    })
  })

  it('posts the comment on the activity when scoped to one', async () => {
    const wrapper = mountComposer({ activity })
    await type(wrapper, '<p>Hallo</p>')

    await send(wrapper).trigger('click')

    expect(post).toHaveBeenCalledWith('/comments', {
      textHtml: '<p>Hallo</p>',
      camp: '/camps/1',
      activity: '/activities/1',
    })
  })

  it('clears the input and announces the new comment', async () => {
    const wrapper = mountComposer()
    await type(wrapper, '<p>Hallo</p>')

    await send(wrapper).trigger('click')
    await wrapper.vm.$nextTick()

    expect(wrapper.vm.textHtml).toBe('')
    expect(wrapper.emitted('created')).toHaveLength(1)
  })

  it.each([['ctrlKey'], ['metaKey']])('posts on %s + enter', async (modifier) => {
    const wrapper = mountComposer()
    await type(wrapper, '<p>Hallo</p>')

    await wrapper.trigger('keydown', { key: 'Enter', [modifier]: true })

    expect(post).toHaveBeenCalledOnce()
  })

  it('does not post an empty comment on ctrl + enter', async () => {
    const wrapper = mountComposer()

    await wrapper.trigger('keydown', { key: 'Enter', ctrlKey: true })

    expect(post).not.toHaveBeenCalled()
  })

  it('does not post on enter alone', async () => {
    const wrapper = mountComposer()
    await type(wrapper, '<p>Hallo</p>')

    await wrapper.trigger('keydown', { key: 'Enter' })

    expect(post).not.toHaveBeenCalled()
  })

  it('does not post twice while the first post is still running', async () => {
    const wrapper = mountComposer()
    await type(wrapper, '<p>Hallo</p>')

    await send(wrapper).trigger('click')
    await wrapper.trigger('keydown', { key: 'Enter', ctrlKey: true })

    expect(post).toHaveBeenCalledOnce()
  })

  it('keeps the text and shows the error when posting fails', async () => {
    const wrapper = mountComposer()
    post.mockRejectedValue(new Error('nope'))
    await type(wrapper, '<p>Hallo</p>')

    await send(wrapper).trigger('click')
    await wrapper.vm.$nextTick()

    expect(wrapper.vm.textHtml).toBe('<p>Hallo</p>')
    expect(wrapper.vm.errorMessages).toHaveLength(1)
    expect(wrapper.emitted('created')).toBeUndefined()
  })
})
