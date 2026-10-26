import { afterEach, beforeEach, describe, expect, it, vi } from 'vitest'
import { mount } from '@vue/test-utils'
import { nextTick, reactive } from 'vue'
import { setupVuetify } from '/tests/setupVuetify.js'
import CommentsPanel from '@/components/comments/CommentsPanel.vue'
import CommentsList from '@/components/comments/CommentsList.vue'
import { commentsState, resetCommentsState } from '@/components/comments/commentsState.js'
import { activityFromRoute, campFromRoute } from '@/router.js'

setupVuetify()

vi.mock('@/router.js', () => ({
  campFromRoute: vi.fn(),
  activityFromRoute: vi.fn(),
  scheduleEntryRoute: vi.fn(),
}))

const currentUser = { _meta: { self: '/users/me' } }

function collaboration(role, userSelf) {
  return {
    _meta: { loading: false },
    role,
    user: () => ({ _meta: { self: userSelf } }),
  }
}

function camp(collaborations) {
  return {
    _meta: { self: '/camps/1' },
    campCollaborations: () => ({ items: collaborations }),
  }
}

function category(short) {
  return { short, name: `category ${short}`, color: '#ff0000' }
}

const activity = {
  _meta: { self: '/activities/1' },
  title: 'Sportolympiade',
  category: () => category('LS'),
}
const otherActivity = {
  _meta: { self: '/activities/2' },
  title: 'Lagerbau',
  category: () => category('LA'),
}

function user(self, displayName) {
  return { _meta: { self }, displayName }
}

const me = user('/users/me', 'Me')
const somebody = user('/users/somebody', 'Somebody')

function comment(
  id,
  {
    on = null,
    orphanDescription = null,
    author = me,
    parent = null,
    resolvedAt = null,
  } = {}
) {
  return {
    _meta: { self: `/comments/${id}` },
    orphanDescription,
    resolvedAt,
    author: () => author,
    ...(on ? { activity: () => on } : {}),
    ...(parent ? { parent: () => parent } : {}),
  }
}

const onActivity = comment(1, { on: activity })
const onOtherActivity = comment(2, { on: otherActivity })
const campLevel = comment(3)
const orphan = comment(4, { orphanDescription: 'LS Sportolympiade' })
const allComments = [onActivity, onOtherActivity, campLevel, orphan]

let scrollToBottom
let reload

function resize(width) {
  window.innerWidth = width
  window.dispatchEvent(new Event('resize'))
}

function commentsApi(items) {
  reload = vi.fn().mockResolvedValue()
  return {
    get: () => ({
      comments: () => ({ _meta: { loading: false }, items, $reload: reload }),
    }),
  }
}

function mountPanel({
  route = reactive({ path: '/camps/1/dashboard', params: { campId: '1' } }),
  items = [],
  mobile = false,
} = {}) {
  scrollToBottom = vi.fn()
  vi.spyOn(CommentsList.methods, 'scrollToBottom').mockImplementation(() =>
    scrollToBottom()
  )
  resize(mobile ? 375 : 1280)
  return mount(CommentsPanel, {
    global: {
      mocks: {
        $t: (key, named) => [key, ...Object.values(named ?? {})].join(' '),
        $route: route,
        $store: { getters: { getLoggedInUser: currentUser } },
        api: commentsApi(items),
      },
      stubs: {
        VNavigationDrawer: { template: '<div class="drawer"><slot /></div>' },
        VDialog: { template: '<div class="dialog"><slot /></div>' },
        CommentThread: true,
        CommentComposer: true,
        CategoryChip: {
          name: 'CategoryChip',
          props: ['category'],
          template: '<span class="chip" />',
        },
      },
    },
  })
}

function listedComments(wrapper) {
  return wrapper
    .findComponent({ name: 'CommentsList' })
    .findAllComponents({ name: 'CommentThread' })
    .flatMap((thread) => [
      thread.props().root._meta.self,
      ...thread.props().replies.map((reply) => reply._meta.self),
    ])
}

describe('CommentsPanel', () => {
  afterEach(() => {
    resetCommentsState()
    vi.restoreAllMocks()
  })
  beforeEach(() => {
    resetCommentsState()
    resize(1280)
    campFromRoute.mockReturnValue(camp([collaboration('guest', '/users/me')]))
    activityFromRoute.mockReturnValue(activity)
  })

  it('renders the panel for a collaborator', () => {
    expect(mountPanel().find('.drawer').exists()).toBe(true)
  })

  it('renders nothing when the route has no camp', () => {
    campFromRoute.mockReturnValue(undefined)

    expect(mountPanel().find('.drawer').exists()).toBe(false)
  })

  it('renders nothing for an external user', () => {
    campFromRoute.mockReturnValue(camp([collaboration('manager', '/users/somebody')]))

    expect(mountPanel().find('.drawer').exists()).toBe(false)
  })

  it('renders nothing while the camp collaborations are still loading', () => {
    campFromRoute.mockReturnValue(
      camp([{ _meta: { loading: true } }, collaboration('manager', '/users/somebody')])
    )

    expect(mountPanel().find('.drawer').exists()).toBe(false)
  })

  it('renders a fullscreen dialog on mobile', () => {
    const wrapper = mountPanel({ mobile: true })

    expect(wrapper.find('.dialog').exists()).toBe(true)
    expect(wrapper.find('.drawer').exists()).toBe(false)
  })

  it('mounts the list only once the panel has been opened', async () => {
    const wrapper = mountPanel()
    expect(wrapper.findComponent(CommentsList).exists()).toBe(false)

    commentsState.open = true
    await nextTick()

    expect(wrapper.findComponent(CommentsList).exists()).toBe(true)
  })

  it('mounts the composer alongside the list', async () => {
    const wrapper = mountPanel()
    commentsState.open = true
    await nextTick()

    expect(wrapper.findComponent({ name: 'CommentComposer' }).exists()).toBe(true)
  })

  it('shows only the scoped activity comments in activity scope', async () => {
    const wrapper = mountPanel({ items: allComments })
    commentsState.open = true
    await nextTick()

    expect(listedComments(wrapper)).toEqual(['/comments/1'])
  })

  it('shows only camp-level comments and orphans in desktop camp scope', async () => {
    activityFromRoute.mockReturnValue(undefined)
    const wrapper = mountPanel({ items: allComments })
    commentsState.open = true
    await nextTick()

    expect(listedComments(wrapper)).toEqual(['/comments/3', '/comments/4'])
  })

  it('shows every comment in mobile camp scope', async () => {
    activityFromRoute.mockReturnValue(undefined)
    const wrapper = mountPanel({ items: allComments, mobile: true })
    commentsState.open = true
    await nextTick()

    expect(listedComments(wrapper)).toEqual([
      '/comments/1',
      '/comments/2',
      '/comments/3',
      '/comments/4',
    ])
  })

  it('asks for context labels in mobile camp scope', async () => {
    activityFromRoute.mockReturnValue(undefined)
    const wrapper = mountPanel({ items: allComments, mobile: true })
    commentsState.open = true
    await nextTick()

    expect(wrapper.findComponent({ name: 'CommentsList' }).props().showContext).toBe(true)
  })

  it('asks for no context labels in desktop camp scope', async () => {
    activityFromRoute.mockReturnValue(undefined)
    const wrapper = mountPanel({ items: allComments })
    commentsState.open = true
    await nextTick()

    expect(wrapper.findComponent({ name: 'CommentsList' }).props().showContext).toBe(
      false
    )
  })

  it('says how many comments are on individual activities', async () => {
    activityFromRoute.mockReturnValue(undefined)
    const wrapper = mountPanel({ items: allComments })
    commentsState.open = true
    await nextTick()

    expect(wrapper.find('[data-testid="comments-elsewhere-notice"]').text()).toBe(
      'components.comments.commentsPanel.activityCommentsElsewhere 2'
    )
  })

  it('says nothing when no comments are on individual activities', async () => {
    activityFromRoute.mockReturnValue(undefined)
    const wrapper = mountPanel({ items: [campLevel, orphan] })
    commentsState.open = true
    await nextTick()

    expect(wrapper.find('[data-testid="comments-elsewhere-notice"]').exists()).toBe(false)
  })

  it('says nothing on mobile, where the list has them all', async () => {
    activityFromRoute.mockReturnValue(undefined)
    const wrapper = mountPanel({ items: allComments, mobile: true })
    commentsState.open = true
    await nextTick()

    expect(wrapper.find('[data-testid="comments-elsewhere-notice"]').exists()).toBe(false)
  })

  it('says nothing in activity scope', async () => {
    const wrapper = mountPanel({ items: allComments })
    commentsState.open = true
    await nextTick()

    expect(wrapper.find('[data-testid="comments-elsewhere-notice"]').exists()).toBe(false)
  })

  it('scopes itself and the composer to the filtered activity', async () => {
    activityFromRoute.mockReturnValue(undefined)
    const wrapper = mountPanel({ items: allComments, mobile: true })
    commentsState.activityFilter = otherActivity
    commentsState.open = true
    await nextTick()

    expect(listedComments(wrapper)).toEqual(['/comments/2'])
    expect(
      wrapper.findComponent({ name: 'CommentComposer' }).props().activity._meta.self
    ).toBe('/activities/2')
  })

  it('shows the category of the scoped activity on mobile', async () => {
    const wrapper = mountPanel({ items: allComments, mobile: true })
    commentsState.open = true
    await nextTick()

    expect(wrapper.findComponent({ name: 'CategoryChip' }).props().category.short).toBe(
      'LS'
    )
    expect(wrapper.text()).toContain('Sportolympiade')
  })

  it('names the activity scope generically on desktop', async () => {
    const wrapper = mountPanel({ items: allComments })
    commentsState.open = true
    await nextTick()

    expect(wrapper.findComponent({ name: 'CategoryChip' }).exists()).toBe(false)
    expect(wrapper.text()).not.toContain('Sportolympiade')
    expect(wrapper.text()).toContain(
      'components.comments.commentsPanel.commentsOnThisActivity'
    )
  })

  it('shows no category in camp scope', async () => {
    activityFromRoute.mockReturnValue(undefined)
    const wrapper = mountPanel({ items: allComments, mobile: true })
    commentsState.open = true
    await nextTick()

    expect(wrapper.findComponent({ name: 'CategoryChip' }).exists()).toBe(false)
  })

  it('names the filtered activity in its header', async () => {
    activityFromRoute.mockReturnValue(undefined)
    const wrapper = mountPanel({ items: allComments, mobile: true })
    commentsState.activityFilter = otherActivity
    commentsState.open = true
    await nextTick()

    expect(wrapper.text()).toContain('Lagerbau')
  })

  it('prefers the route activity over the filter', async () => {
    const wrapper = mountPanel({ items: allComments })
    commentsState.activityFilter = otherActivity
    commentsState.open = true
    await nextTick()

    expect(listedComments(wrapper)).toEqual(['/comments/1'])
  })

  it('scrolls to the bottom after a comment was created', async () => {
    const wrapper = mountPanel({ items: allComments })
    commentsState.open = true
    await nextTick()

    wrapper.findComponent({ name: 'CommentComposer' }).vm.$emit('created')

    await vi.waitFor(() => expect(scrollToBottom).toHaveBeenCalled())
    expect(reload).toHaveBeenCalled()
  })

  it('closes when the route changes to another camp', async () => {
    const route = reactive({ path: '/camps/1/dashboard', params: { campId: '1' } })
    mountPanel({ route })
    commentsState.open = true

    route.params.campId = '2'
    await vi.waitFor(() => expect(commentsState.open).toBe(false))
  })

  it('closes on mobile when a link inside it navigates away', async () => {
    const route = reactive({ path: '/camps/1/dashboard', params: { campId: '1' } })
    mountPanel({ route, mobile: true })
    commentsState.open = true
    await nextTick()

    route.path = '/camps/1/activities/1'
    await vi.waitFor(() => expect(commentsState.open).toBe(false))
  })

  it.each(['open', 'all'])(
    'shows a newly created comment when the selected filter was %s',
    async (status) => {
      const items = reactive([])
      const wrapper = mountPanel({ items })
      reload.mockImplementation(async () => {
        items.push(comment(99, { on: activity }))
      })
      commentsState.open = true
      commentsState.statusFilter = status
      await nextTick()

      wrapper.findComponent({ name: 'CommentComposer' }).vm.$emit('created')

      await vi.waitFor(() => expect(scrollToBottom).toHaveBeenCalled())
      expect(reload).toHaveBeenCalled()
      expect(commentsState.statusFilter).toBe(status)
      expect(listedComments(wrapper)).toContain('/comments/99')
    }
  )

  it('hides the composer under Resolved and restores it under Open', async () => {
    const wrapper = mountPanel()
    commentsState.open = true
    await nextTick()
    expect(wrapper.findComponent({ name: 'CommentComposer' }).exists()).toBe(true)

    commentsState.statusFilter = 'resolved'
    await nextTick()
    expect(wrapper.findComponent({ name: 'CommentComposer' }).exists()).toBe(false)
    expect(commentsState.statusFilter).toBe('resolved')

    commentsState.statusFilter = 'open'
    await nextTick()
    expect(wrapper.findComponent({ name: 'CommentComposer' }).exists()).toBe(true)
  })

  it('stays open on desktop while navigating', async () => {
    const route = reactive({ path: '/camps/1/dashboard', params: { campId: '1' } })
    mountPanel({ route })
    commentsState.open = true
    await nextTick()

    route.path = '/camps/1/activities/1'
    await nextTick()
    expect(commentsState.open).toBe(true)
  })

  it('drops the activity filter when it is closed', async () => {
    mountPanel()
    commentsState.open = true
    commentsState.activityFilter = activity
    await nextTick()

    commentsState.open = false
    await vi.waitFor(() => expect(commentsState.activityFilter).toBe(null))
  })

  describe('threads', () => {
    const resolvedRoot = comment(5, {
      on: activity,
      resolvedAt: '2026-08-01T12:00:00+00:00',
    })
    const replyToResolved = comment(6, { on: activity, parent: resolvedRoot })
    const openRoot = comment(7, { on: activity, author: somebody })
    const replyToOpen = comment(8, { on: activity, parent: openRoot })
    const thread = [resolvedRoot, replyToResolved, openRoot, replyToOpen]

    it('leaves out resolved threads by default', async () => {
      const wrapper = mountPanel({ items: thread })
      commentsState.open = true
      await nextTick()

      expect(listedComments(wrapper)).toEqual(['/comments/7', '/comments/8'])
    })

    it('lists resolved threads in full under "All"', async () => {
      const wrapper = mountPanel({ items: thread })
      commentsState.statusFilter = 'all'
      commentsState.open = true
      await nextTick()

      expect(listedComments(wrapper)).toEqual([
        '/comments/5',
        '/comments/6',
        '/comments/7',
        '/comments/8',
      ])
    })

    it('switches to "All" through the toggle in its header', async () => {
      const wrapper = mountPanel({ items: thread })
      commentsState.open = true
      await nextTick()

      const chips = wrapper
        .find('[data-testid="comments-resolved-toggle"]')
        .findAll('.v-chip')
      await chips[2].trigger('click')

      expect(commentsState.statusFilter).toBe('all')
      expect(listedComments(wrapper)).toContain('/comments/5')
    })

    it('shows only resolved threads through the middle filter button', async () => {
      const wrapper = mountPanel({ items: thread })
      commentsState.open = true
      await nextTick()

      const chips = wrapper
        .find('[data-testid="comments-resolved-toggle"]')
        .findAll('.v-chip')
      expect(chips.map((chip) => chip.text())).toEqual([
        'components.comments.commentsPanel.open 2',
        'components.comments.commentsPanel.resolved 2',
        'components.comments.commentsPanel.all',
      ])
      await chips[1].trigger('click')

      expect(commentsState.statusFilter).toBe('resolved')
      expect(listedComments(wrapper)).toEqual(['/comments/5', '/comments/6'])
    })

    it.each([
      [false, 1, 1],
      [true, 3, 3],
    ])('counts comments in camp scope (mobile: %s)', async (mobile, open, resolved) => {
      activityFromRoute.mockReturnValue(undefined)
      const wrapper = mountPanel({
        mobile,
        items: [
          comment(20),
          comment(21, { resolvedAt: '2026-08-01T12:00:00+00:00' }),
          ...thread,
        ],
      })
      commentsState.open = true
      await nextTick()

      expect(wrapper.get('[data-testid="comments-open-count"]').text()).toBe(`${open}`)
      expect(wrapper.get('[data-testid="comments-resolved-count"]').text()).toBe(
        `${resolved}`
      )
    })

    it('updates both counts when a comment is resolved, independently of the selected filter', async () => {
      const items = reactive([comment(30, { on: activity })])
      const wrapper = mountPanel({ items })
      commentsState.open = true
      await nextTick()

      expect(wrapper.get('[data-testid="comments-open-count"]').text()).toBe('1')
      expect(wrapper.find('[data-testid="comments-resolved-count"]').exists()).toBe(false)

      items[0].resolvedAt = '2026-08-01T12:00:00+00:00'
      commentsState.statusFilter = 'all'
      await nextTick()

      expect(wrapper.find('[data-testid="comments-open-count"]').exists()).toBe(false)
      expect(wrapper.get('[data-testid="comments-resolved-count"]').text()).toBe('1')
      expect(wrapper.find('[data-testid="comments-all-count"]').exists()).toBe(false)
    })

    it.each([
      ['noResolvedComments', 'resolved', [openRoot, replyToOpen]],
      ['noOpenCommentsWithResolved', 'open', [resolvedRoot, replyToResolved]],
      ['noOpenComments', 'open', []],
      [
        'noOpenComments',
        'open',
        [comment(31, { on: otherActivity, resolvedAt: '2026-08-01T12:00:00+00:00' })],
      ],
    ])('shows %s for %s (case %#)', async (message, statusFilter, items) => {
      const wrapper = mountPanel({ items })
      commentsState.statusFilter = statusFilter
      commentsState.open = true
      await nextTick()

      expect(listedComments(wrapper)).toEqual([])
      expect(wrapper.findComponent({ name: 'CommentsList' }).props().emptyText).toBe(
        `components.comments.commentsList.${message}`
      )
    })

    it('counts open comments on activities with their replies, but not resolved ones', async () => {
      activityFromRoute.mockReturnValue(undefined)
      const wrapper = mountPanel({ items: [campLevel, ...thread] })
      commentsState.open = true
      await nextTick()

      expect(wrapper.find('[data-testid="comments-elsewhere-notice"]').text()).toBe(
        'components.comments.commentsPanel.activityCommentsElsewhere 2'
      )
    })

    it('counts the same comments elsewhere under "All"', async () => {
      activityFromRoute.mockReturnValue(undefined)
      const wrapper = mountPanel({ items: [campLevel, ...thread] })
      commentsState.statusFilter = 'all'
      commentsState.open = true
      await nextTick()

      expect(wrapper.find('[data-testid="comments-elsewhere-notice"]').text()).toBe(
        'components.comments.commentsPanel.activityCommentsElsewhere 2'
      )
    })

    it('reloads the comments after a reply', async () => {
      const wrapper = mountPanel({ items: thread })
      commentsState.open = true
      await nextTick()

      wrapper.findComponent({ name: 'CommentsList' }).vm.$emit('replied')

      expect(reload).toHaveBeenCalled()
      expect(scrollToBottom).not.toHaveBeenCalled()
    })

    it('keeps showing resolved threads after it was closed', async () => {
      mountPanel({ items: thread })
      commentsState.open = true
      commentsState.statusFilter = 'all'
      await nextTick()

      commentsState.open = false
      await nextTick()
      expect(commentsState.statusFilter).toBe('all')
    })

    it('goes back to "Open" on camp change', async () => {
      const route = reactive({ path: '/camps/1/dashboard', params: { campId: '1' } })
      mountPanel({ route })
      commentsState.open = true
      commentsState.statusFilter = 'all'

      route.params.campId = '2'
      await vi.waitFor(() => expect(commentsState.statusFilter).toBe('open'))
    })
  })
})
