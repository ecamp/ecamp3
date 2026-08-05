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

const activity = { _meta: { self: '/activities/1' } }

function commentsApi(comments) {
  return { get: () => ({ comments: () => comments }) }
}

function mountPanel(
  route = reactive({ params: { campId: '1' } }),
  api = commentsApi({ _meta: { loading: false }, items: [], $reload: () => {} })
) {
  return mount(CommentsPanel, {
    global: {
      mocks: {
        $t: (key) => key,
        $route: route,
        $store: { getters: { getLoggedInUser: currentUser } },
        api,
      },
      stubs: {
        VNavigationDrawer: { template: '<div class="drawer"><slot /></div>' },
        CommentsList: true,
        CommentComposer: true,
      },
    },
  })
}

describe('CommentsPanel', () => {
  beforeEach(() => {
    commentsState.open = false
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

  it('mounts the list only once the panel has been opened', async () => {
    const wrapper = mountPanel()
    expect(wrapper.find('comments-list-stub').exists()).toBe(false)

    commentsState.open = true
    await nextTick()

    expect(wrapper.find('comments-list-stub').exists()).toBe(true)
  })

  it('mounts the composer alongside the list', async () => {
    const wrapper = mountPanel()
    expect(wrapper.find('comment-composer-stub').exists()).toBe(false)

    commentsState.open = true
    await nextTick()

    expect(wrapper.find('comment-composer-stub').exists()).toBe(true)
  })

  it('reloads the comments once one was created', async () => {
    const reload = vi.fn()
    const wrapper = mountPanel(undefined, commentsApi({ $reload: reload }))
    commentsState.open = true
    await nextTick()

    wrapper.findComponent({ name: 'CommentComposer' }).vm.$emit('created')

    expect(reload).toHaveBeenCalledOnce()
  })

  it('hands the camp comments to the list', async () => {
    const comments = { _meta: { loading: false }, items: [] }
    const wrapper = mountPanel(undefined, commentsApi(comments))

    commentsState.open = true
    await nextTick()

    expect(wrapper.findComponent({ name: 'CommentsList' }).props().comments).toBe(
      comments
    )
  })

  it('closes when the route changes to another camp', async () => {
    const route = reactive({ params: { campId: '1' } })
    mountPanel(route)
    commentsState.open = true

    route.params.campId = '2'
    await vi.waitFor(() => expect(commentsState.open).toBe(false))
  })
})
