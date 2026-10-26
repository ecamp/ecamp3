import { afterEach, describe, expect, it, vi } from 'vitest'
import { enableAutoUnmount, mount } from '@vue/test-utils'
import { setupVuetify } from '/tests/setupVuetify.js'
import dayjs from '@/common/helpers/dayjs.js'
import CommentThread from '@/components/comments/CommentThread.vue'
import { reactive } from 'vue'
import flushPromises from 'flush-promises'

setupVuetify()

const DATE_FORMAT = 'YYYY-MM-DD HH:mm'
const REMOVAL_DURATION = 2000
enableAutoUnmount(afterEach)
afterEach(() => {
  vi.useRealTimers()
  window.localStorage.clear()
})

const currentUser = { _meta: { self: '/users/me' } }
const camp = { _meta: { self: '/camps/1' } }
const activity = { _meta: { self: '/activities/1' } }

function collaboration(userSelf, displayName) {
  return {
    _meta: { self: `/camp_collaborations${userSelf}` },
    status: 'established',
    user: () => ({ _meta: { self: userSelf }, displayName }),
  }
}

function comment(
  id,
  {
    author = collaboration('/users/me', 'Me'),
    on = activity,
    resolvedAt = null,
    resolvedBy,
    authorDisplayName = null,
  } = {}
) {
  return {
    _meta: { self: `/comments/${id}` },
    textHtml: `<p>comment ${id}</p>`,
    createTime: '2026-08-01T12:00:00+00:00',
    orphanDescription: null,
    resolvedAt,
    authorDisplayName,
    ...(author ? { author: () => author } : {}),
    camp: () => camp,
    ...(on ? { activity: () => on } : {}),
    ...(resolvedBy ? { resolvedBy: () => resolvedBy } : {}),
  }
}

let patch

function mountThread({
  root = comment(1),
  replies = [],
  showContext = false,
  camp: campProp = camp,
  statusFilter = 'open',
} = {}) {
  patch = vi.fn().mockResolvedValue({})
  return mount(CommentThread, {
    props: { root, replies, showContext, camp: campProp, statusFilter },
    global: {
      mocks: {
        $t: (key, named) =>
          key.startsWith('global.datetime.')
            ? DATE_FORMAT
            : [key, ...Object.values(named ?? {})].join(' '),
        $date: dayjs,
        $store: { getters: { getLoggedInUser: currentUser } },
        api: { patch },
      },
      stubs: {
        TiptapEditor: { template: '<div class="text" />' },
        ScheduleEntryLinks: true,
        CommentComposer: {
          name: 'CommentComposer',
          props: {
            camp: Object,
            activity: Object,
            parent: Object,
            cancelable: Boolean,
            autofocus: Boolean,
          },
          template: '<div class="composer" />',
        },
      },
    },
  })
}

function renderedComments(wrapper) {
  return wrapper
    .findAllComponents({ name: 'CommentCard' })
    .map((card) => card.props().comment._meta.self)
}

function button(wrapper, testid) {
  return wrapper.find(`[data-testid="${testid}"]`)
}

function retained(wrapper) {
  return wrapper.emitted('retain')?.at(-1)?.[0] ?? false
}

describe('CommentThread', () => {
  it('reopens a newly resolved thread', async () => {
    const root = reactive(comment(10))
    const wrapper = mountThread({ root })
    patch.mockImplementation(async (_, { resolved }) => {
      root.resolvedAt = resolved ? '2026-08-02T12:00:00+00:00' : null
    })

    await button(wrapper, 'comment-thread-resolve').trigger('click')
    await flushPromises()
    await button(wrapper, 'comment-thread-resolve').trigger('click')
    await flushPromises()

    expect(root.resolvedAt).toBe(null)
    expect(retained(wrapper)).toBe(false)
    expect(patch).toHaveBeenLastCalledWith(root, { resolved: false })
  })

  it.each([
    [750, 1250],
    [1950, 1000],
  ])(
    'resumes with %i ms elapsed and at least %i ms remaining after hover',
    async (elapsed, remaining) => {
      vi.useFakeTimers({ toFake: ['setInterval', 'clearInterval'] })
      const root = reactive(comment(10))
      const wrapper = mountThread({ root })
      patch.mockImplementation(async () => {
        root.resolvedAt = '2026-08-02T12:00:00+00:00'
      })
      const resolve = button(wrapper, 'comment-thread-resolve')
      const hovered = vi.spyOn(resolve.element, 'matches').mockReturnValue(false)
      await resolve.trigger('click')
      await flushPromises()
      await vi.advanceTimersByTimeAsync(elapsed)
      hovered.mockReturnValue(true)

      const progress = wrapper.findComponent({ name: 'VProgressCircular' })
      const pausedProgress = Math.min(progress.props().modelValue, 50)
      await vi.advanceTimersByTimeAsync(REMOVAL_DURATION * 2)

      expect(retained(wrapper)).toBe(true)
      expect(progress.props().modelValue).toBe(pausedProgress)

      hovered.mockReturnValue(false)
      await vi.advanceTimersByTimeAsync(remaining - 1)
      expect(retained(wrapper)).toBe(true)
      await vi.waitFor(() => expect(progress.props().modelValue).toBe(100), {
        interval: 10,
        timeout: 100,
      })
      expect(retained(wrapper)).toBe(true)
      await vi.advanceTimersToNextTimerAsync()
      expect(retained(wrapper)).toBe(false)
    }
  )

  it('clears a paused countdown when the thread is unmounted', async () => {
    vi.useFakeTimers({ toFake: ['setInterval', 'clearInterval'] })
    const root = reactive(comment(10))
    const wrapper = mountThread({ root })
    patch.mockImplementation(async () => {
      root.resolvedAt = '2026-08-02T12:00:00+00:00'
    })
    await button(wrapper, 'comment-thread-resolve').trigger('click')
    await flushPromises()
    await vi.advanceTimersByTimeAsync(750)
    vi.spyOn(
      button(wrapper, 'comment-thread-resolve').element,
      'matches'
    ).mockReturnValue(true)

    wrapper.unmount()
    await vi.advanceTimersByTimeAsync(REMOVAL_DURATION)

    expect(retained(wrapper)).toBe(false)
    expect(vi.getTimerCount()).toBe(0)
  })

  it.each(['all', 'resolved'])(
    'does not retain threads while viewing %s',
    async (status) => {
      const wrapper = mountThread({ statusFilter: status })
      await button(wrapper, 'comment-thread-resolve').trigger('click')
      await flushPromises()
      expect(retained(wrapper)).toBe(false)
    }
  )

  it('renders the root comment followed by its replies', () => {
    const wrapper = mountThread({ replies: [comment(2), comment(3)] })

    expect(renderedComments(wrapper)).toEqual([
      '/comments/1',
      '/comments/2',
      '/comments/3',
    ])
  })

  it('shows the context on the root comment only', () => {
    const wrapper = mountThread({ replies: [comment(2)], showContext: true })

    const cards = wrapper.findAllComponents({ name: 'CommentCard' })
    expect(cards.map((card) => card.props().showContext)).toEqual([true, false])
  })

  it('disables deleting the root comment while it has replies by others and hides reply metadata', () => {
    const wrapper = mountThread({
      replies: [
        comment(2),
        comment(3, { author: collaboration('/users/other', 'Other') }),
      ],
    })

    const cards = wrapper.findAllComponents({ name: 'CommentCard' })
    expect(cards[0].props().hasReplies).toBe(true)
    expect(cards[0].find('.ec-comment-card__delete').attributes('disabled')).toBeDefined()
    expect(cards[1].find('.ec-comment-card__meta').exists()).toBe(true)
  })

  it('allows deleting a root comment whose replies are all by its author', () => {
    const wrapper = mountThread({ replies: [comment(2), comment(3)] })

    const root = wrapper.findAllComponents({ name: 'CommentCard' })[0]
    expect(root.props().hasRepliesByOthers).toBe(false)
    expect(root.find('.ec-comment-card__delete').attributes('disabled')).toBeUndefined()
  })

  it('disables deleting a root comment once somebody else replied', () => {
    const wrapper = mountThread({
      replies: [
        comment(2),
        comment(3, { author: collaboration('/users/other', 'Other') }),
      ],
    })

    const root = wrapper.findAllComponents({ name: 'CommentCard' })[0]
    expect(root.props().hasRepliesByOthers).toBe(true)
    expect(root.find('.ec-comment-card__delete').attributes('disabled')).toBeDefined()
  })

  it('shows the saved name of an author who left the camp, without a delete button', () => {
    const wrapper = mountThread({
      root: comment(1, { author: null, authorDisplayName: 'Former Leader' }),
    })

    const card = wrapper.findComponent({ name: 'CommentCard' })
    expect(card.text()).toContain('Former Leader')
    expect(card.find('.ec-comment-card__delete').exists()).toBe(false)
  })

  it('names an author whose user was deleted completely neutrally', () => {
    const wrapper = mountThread({ root: comment(1, { author: null }) })

    const card = wrapper.findComponent({ name: 'CommentCard' })
    expect(card.text()).toContain('components.comments.commentCard.deletedAuthor')
    expect(card.find('.ec-comment-card__delete').exists()).toBe(false)
  })

  it('allows deleting a root comment without replies', () => {
    const wrapper = mountThread()

    const card = wrapper.findComponent({ name: 'CommentCard' })
    expect(card.find('.ec-comment-card__delete').attributes('disabled')).toBeUndefined()
  })

  it('opens a reply composer posting into the thread', async () => {
    const wrapper = mountThread()
    expect(wrapper.findComponent({ name: 'CommentComposer' }).exists()).toBe(false)

    await button(wrapper, 'comment-thread-reply').trigger('click')

    const composer = wrapper.findComponent({ name: 'CommentComposer' })
    expect(composer.props().parent._meta.self).toBe('/comments/1')
  })

  it('closes the reply composer and tells its parent once the reply was posted', async () => {
    const wrapper = mountThread()
    await button(wrapper, 'comment-thread-reply').trigger('click')

    wrapper.findComponent({ name: 'CommentComposer' }).vm.$emit('created')
    await wrapper.vm.$nextTick()

    expect(wrapper.emitted('replied')).toHaveLength(1)
    expect(wrapper.findComponent({ name: 'CommentComposer' }).exists()).toBe(false)
  })

  it('closes the reply composer when it is cancelled', async () => {
    const wrapper = mountThread()
    await button(wrapper, 'comment-thread-reply').trigger('click')

    const composer = wrapper.findComponent({ name: 'CommentComposer' })
    expect(composer.props().cancelable).toBe(true)
    composer.vm.$emit('cancel')
    await wrapper.vm.$nextTick()

    expect(wrapper.findComponent({ name: 'CommentComposer' }).exists()).toBe(false)
    expect(button(wrapper, 'comment-thread-reply').exists()).toBe(true)
  })

  it('lets a new reply composer focus itself', async () => {
    const wrapper = mountThread()

    await button(wrapper, 'comment-thread-reply').trigger('click')

    expect(wrapper.findComponent({ name: 'CommentComposer' }).props().autofocus).toBe(
      true
    )
  })

  it('resolves an open thread', async () => {
    const root = comment(1)
    const wrapper = mountThread({ root })

    expect(button(wrapper, 'comment-thread-resolve').attributes('aria-label')).toBe(
      'components.comments.commentThread.resolve'
    )
    await button(wrapper, 'comment-thread-resolve').trigger('click')

    expect(patch).toHaveBeenCalledWith(root, { resolved: true })
  })

  it('shows who resolved a thread and when', () => {
    const wrapper = mountThread({
      root: comment(1, {
        resolvedAt: '2026-08-02T12:00:00+00:00',
        resolvedBy: collaboration('/users/coach', 'Coach'),
      }),
    })

    expect(button(wrapper, 'comment-thread-resolved').text()).toContain(
      'components.comments.commentThread.resolvedBy Coach ·'
    )
    expect(wrapper.findAllComponents({ name: 'CommentTime' }).at(-1).props().value).toBe(
      '2026-08-02T12:00:00+00:00'
    )
  })

  it('marks a resolver who is inactive in the camp', () => {
    const wrapper = mountThread({
      root: comment(1, {
        resolvedAt: '2026-08-02T12:00:00+00:00',
        resolvedBy: { ...collaboration('/users/coach', 'Coach'), status: 'inactive' },
      }),
    })

    expect(button(wrapper, 'comment-thread-resolved').text()).toContain(
      'components.comments.commentThread.resolvedBy Coach (entity.campCollaboration.status.inactive) ·'
    )
  })

  it('shows when a thread was resolved even if the resolver is gone', () => {
    const wrapper = mountThread({
      root: comment(1, { resolvedAt: '2026-08-02T12:00:00+00:00' }),
    })

    expect(button(wrapper, 'comment-thread-resolved').text()).toContain(
      'components.comments.commentThread.resolved ·'
    )
    expect(wrapper.findAllComponents({ name: 'CommentTime' }).at(-1).props().value).toBe(
      '2026-08-02T12:00:00+00:00'
    )
  })

  it('renders a resolved thread in full', () => {
    const wrapper = mountThread({
      root: comment(1, { resolvedAt: '2026-08-02T12:00:00+00:00' }),
      replies: [comment(2), comment(3)],
    })

    expect(renderedComments(wrapper)).toEqual([
      '/comments/1',
      '/comments/2',
      '/comments/3',
    ])
  })

  it('shows an error when reopening failed', async () => {
    const root = comment(1, { resolvedAt: '2026-08-02T12:00:00+00:00' })
    const wrapper = mountThread({ root })
    patch.mockRejectedValue(new Error('network down'))

    await button(wrapper, 'comment-thread-resolve').trigger('click')

    expect(patch).toHaveBeenCalledWith(root, { resolved: false })
    await vi.waitFor(() => expect(wrapper.text()).toContain('network down'))
  })

  it('reopens a resolved thread', async () => {
    const root = comment(1, { resolvedAt: '2026-08-02T12:00:00+00:00' })
    const wrapper = mountThread({ root })

    expect(button(wrapper, 'comment-thread-resolve').attributes('aria-label')).toBe(
      'components.comments.commentThread.reopen'
    )
    await button(wrapper, 'comment-thread-resolve').trigger('click')

    expect(patch).toHaveBeenCalledWith(root, { resolved: false })
  })

  it('offers no reply on a resolved thread', () => {
    const wrapper = mountThread({
      root: comment(1, { resolvedAt: '2026-08-02T12:00:00+00:00' }),
    })

    expect(button(wrapper, 'comment-thread-reply').exists()).toBe(false)
  })

  it('keeps the resolved text visible and replaces its icon while reopening', async () => {
    const wrapper = mountThread({
      root: comment(1, { resolvedAt: '2026-08-02T12:00:00+00:00' }),
    })
    let finishReopening
    patch.mockImplementation(() => new Promise((resolve) => (finishReopening = resolve)))
    const resolvedText = button(wrapper, 'comment-thread-resolved').text()

    await button(wrapper, 'comment-thread-resolve').trigger('click')

    const status = button(wrapper, 'comment-thread-resolved')
    expect(status.text()).toBe(resolvedText)
    expect(status.findComponent({ name: 'VProgressCircular' }).props()).toMatchObject({
      indeterminate: true,
      size: '16',
      width: '2',
    })
    expect(status.findComponent({ name: 'VIcon' }).exists()).toBe(false)
    expect(button(wrapper, 'comment-thread-resolve').attributes('disabled')).toBeDefined()

    await button(wrapper, 'comment-thread-resolve').trigger('click')
    expect(patch).toHaveBeenCalledOnce()

    finishReopening()
    await vi.waitFor(() =>
      expect(status.findComponent({ name: 'VProgressCircular' }).exists()).toBe(false)
    )
    expect(status.find('.ec-comment-thread__resolved-icon').exists()).toBe(true)
  })

  it('shows an error when resolving failed', async () => {
    const wrapper = mountThread()
    patch.mockRejectedValue(new Error('network down'))

    await button(wrapper, 'comment-thread-resolve').trigger('click')
    await vi.waitFor(() => expect(wrapper.text()).toContain('network down'))
    expect(retained(wrapper)).toBe(false)
  })
})
