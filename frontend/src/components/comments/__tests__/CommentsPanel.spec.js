import { beforeEach, describe, expect, it, vi } from 'vitest'
import { mount } from '@vue/test-utils'
import { nextTick, reactive } from 'vue'
import { setupVuetify } from '/tests/setupVuetify.js'
import CommentsPanel from '@/components/comments/CommentsPanel.vue'
import { commentsState } from '@/components/comments/commentsState.js'
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

function comment(id, { on = null, orphanDescription = null } = {}) {
  return {
    _meta: { self: `/comments/${id}` },
    orphanDescription,
    ...(on ? { activity: () => on } : {}),
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
        CommentsList: {
          name: 'CommentsList',
          props: ['comments', 'loading', 'showContext'],
          template: '<div class="list"><slot name="after" /></div>',
          methods: { scrollToBottom: () => scrollToBottom() },
        },
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
    .props()
    .comments.map((c) => c._meta.self)
}

describe('CommentsPanel', () => {
  beforeEach(() => {
    commentsState.open = false
    commentsState.activityFilter = null
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
    expect(wrapper.find('.list').exists()).toBe(false)

    commentsState.open = true
    await nextTick()

    expect(wrapper.find('.list').exists()).toBe(true)
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
})
