import { afterEach, describe, expect, it, vi } from 'vitest'
import { enableAutoUnmount, mount } from '@vue/test-utils'
import { setupVuetify } from '/tests/setupVuetify.js'
import dayjs from '@/common/helpers/dayjs.js'
import CommentThread from '@/components/comments/CommentThread.vue'
import { clearToasts, visibleToasts } from '@/components/toast/useToast.js'
import { commentsState } from '@/components/comments/commentsState.js'

setupVuetify()

const DATE_FORMAT = 'YYYY-MM-DD HH:mm'
enableAutoUnmount(afterEach)
afterEach(() => {
  clearToasts()
  commentsState.showResolved = false
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

function mountThread({ root = comment(1), replies = [], showContext = false } = {}) {
  patch = vi.fn().mockResolvedValue({})
  return mount(CommentThread, {
    props: { root, replies, showContext },
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

describe('CommentThread', () => {
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

  it('disables deleting the root comment while it has replies by others', () => {
    const wrapper = mountThread({
      replies: [
        comment(2),
        comment(3, { author: collaboration('/users/other', 'Other') }),
      ],
    })

    const cards = wrapper.findAllComponents({ name: 'CommentCard' })
    expect(cards[0].props().hasReplies).toBe(true)
    expect(cards[0].find('.ec-comment-card__delete').attributes('disabled')).toBeDefined()
    expect(
      cards[1].find('.ec-comment-card__delete').attributes('disabled')
    ).toBeUndefined()
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

  it('says where a resolved thread went and offers to undo it', async () => {
    const root = comment(1)
    const wrapper = mountThread({ root })

    await button(wrapper, 'comment-thread-resolve').trigger('click')
    await vi.waitFor(() => expect(visibleToasts.value).toHaveLength(1))

    const [toast] = visibleToasts.value
    expect(toast.type).toBe('info')
    expect(toast.content).toBe('components.comments.commentThread.resolvedToast')
    expect(toast.actions.map((action) => action.label)).toEqual([
      'global.button.undo',
      'components.comments.commentThread.showAll',
    ])

    wrapper.unmount()
    toast.actions[0].onClick()
    expect(patch).toHaveBeenLastCalledWith(root, { resolved: false })
  })

  it('switches the panel to show resolved threads from the toast', async () => {
    const wrapper = mountThread()
    await button(wrapper, 'comment-thread-resolve').trigger('click')
    await vi.waitFor(() => expect(visibleToasts.value).toHaveLength(1))

    visibleToasts.value[0].actions[1].onClick()

    expect(commentsState.showResolved).toBe(true)
  })

  it('offers no way to "All" when the panel already shows it', async () => {
    commentsState.showResolved = true
    const wrapper = mountThread()
    await button(wrapper, 'comment-thread-resolve').trigger('click')
    await vi.waitFor(() => expect(visibleToasts.value).toHaveLength(1))

    expect(visibleToasts.value[0].actions.map((action) => action.label)).toEqual([
      'global.button.undo',
    ])
  })

  it('reports when undoing failed', async () => {
    const wrapper = mountThread()
    await button(wrapper, 'comment-thread-resolve').trigger('click')
    await vi.waitFor(() => expect(visibleToasts.value).toHaveLength(1))
    patch.mockRejectedValue(new Error('network down'))

    visibleToasts.value[0].actions[0].onClick()

    await vi.waitFor(() =>
      expect(visibleToasts.value.some((toast) => toast.type === 'error')).toBe(true)
    )
  })

  it('shows no toast when reopening', async () => {
    const wrapper = mountThread({
      root: comment(1, { resolvedAt: '2026-08-02T12:00:00+00:00' }),
    })

    await button(wrapper, 'comment-thread-resolve').trigger('click')
    await vi.waitFor(() => expect(patch).toHaveBeenCalled())

    expect(visibleToasts.value).toHaveLength(0)
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
  })
})
