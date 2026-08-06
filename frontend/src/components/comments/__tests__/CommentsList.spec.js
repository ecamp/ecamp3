import { afterEach, describe, expect, it, vi } from 'vitest'
import { enableAutoUnmount, mount } from '@vue/test-utils'
import { setupVuetify } from '/tests/setupVuetify.js'
import CommentsList from '@/components/comments/CommentsList.vue'
import {
  commentsState,
  focusActivityComments,
} from '@/components/comments/commentsState.js'

setupVuetify()
enableAutoUnmount(afterEach)

afterEach(() => {
  commentsState.focusedActivity = null
})

const currentUser = { _meta: { self: '/users/me' } }
function activityFixture(uri) {
  return { _meta: { self: uri, load: Object.assign(Promise.resolve(), { uri }) } }
}

const activity = activityFixture('/activities/1')
const otherActivity = activityFixture('/activities/2')

function scheduleEntry(on) {
  return { activity: () => on }
}

function camp(scheduleEntries, loading = false) {
  return {
    _meta: { self: '/camps/1' },
    periods: () => ({
      _meta: { loading },
      items: [
        { scheduleEntries: () => ({ _meta: { loading }, items: scheduleEntries }) },
      ],
    }),
  }
}

function comment(
  id,
  { authorSelf = '/users/me', on = activity, orphanDescription = null } = {}
) {
  return {
    _meta: { self: `/comments/${id}` },
    textHtml: `<p>comment ${id}</p>`,
    createTime: `2026-08-0${id}T12:00:00+00:00`,
    orphanDescription,
    author: () => ({ _meta: { self: authorSelf }, displayName: `author ${authorSelf}` }),
    ...(on ? { activity: () => on } : {}),
  }
}

function mountList({
  loading = false,
  items = [],
  scope = activity,
  scheduleEntries = [scheduleEntry(activity), scheduleEntry(otherActivity)],
  programLoading = false,
  attachTo = undefined,
} = {}) {
  return mount(CommentsList, {
    attachTo,
    props: {
      camp: camp(scheduleEntries, programLoading),
      activity: scope,
      comments: { _meta: { loading }, items },
    },
    global: {
      mocks: {
        $t: (key, named) => [key, ...Object.values(named ?? {})].join(' '),
        $date: (value) => ({ format: () => value }),
        $store: { getters: { getLoggedInUser: currentUser } },
      },
      stubs: {
        TiptapEditor: { template: '<div class="text"><slot /></div>' },
        ScheduleEntryLinks: {
          name: 'ScheduleEntryLinks',
          props: ['activityPromise'],
          template: '<a />',
        },
      },
    },
  })
}

function renderedComments(wrapper) {
  return wrapper
    .findAllComponents({ name: 'CommentCard' })
    .map((c) => c.props().comment._meta.self)
}

function focusedGroupActivity(wrapper) {
  const focused = wrapper.find('.ec-comments-list__group--focused')
  return wrapper
    .findAllComponents({ name: 'ScheduleEntryLinks' })
    .find((link) => focused.element.contains(link.element))
    ?.props().activityPromise.uri
}

function groupedActivities(wrapper) {
  return wrapper
    .findAllComponents({ name: 'ScheduleEntryLinks' })
    .map((link) => link.props().activityPromise.uri)
}

describe('CommentsList', () => {
  it('shows skeletons while the collection is loading', () => {
    const wrapper = mountList({ loading: true })

    expect(wrapper.findAll('.v-skeleton-loader')).toHaveLength(2)
    expect(wrapper.findAllComponents({ name: 'CommentCard' })).toHaveLength(0)
  })

  it('shows skeletons while the programme is still loading in camp scope', () => {
    const wrapper = mountList({ scope: null, items: [comment(1)], programLoading: true })

    expect(wrapper.findAll('.v-skeleton-loader')).toHaveLength(2)
    expect(wrapper.findAllComponents({ name: 'CommentCard' })).toHaveLength(0)
  })

  it('does not wait for the programme in activity scope', () => {
    const wrapper = mountList({ items: [comment(1)], programLoading: true })

    expect(renderedComments(wrapper)).toEqual(['/comments/1'])
  })

  it('shows an empty state when the scope has no comments', () => {
    const wrapper = mountList({ items: [comment(1, { on: otherActivity })] })

    expect(wrapper.text()).toContain('components.comments.commentsList.empty')
    expect(wrapper.findAllComponents({ name: 'CommentCard' })).toHaveLength(0)
  })

  it('keeps the order the API returned', () => {
    const wrapper = mountList({ items: [comment(1), comment(2), comment(3)] })

    expect(renderedComments(wrapper)).toEqual([
      '/comments/1',
      '/comments/2',
      '/comments/3',
    ])
  })

  it('shows only the comments of the scoped activity', () => {
    const wrapper = mountList({
      items: [comment(1), comment(2, { on: otherActivity }), comment(3, { on: null })],
    })

    expect(renderedComments(wrapper)).toEqual(['/comments/1'])
  })

  it('renders a flat list in activity scope', () => {
    const wrapper = mountList({ items: [comment(1), comment(2)] })

    expect(wrapper.findAll('.ec-comments-list__group')).toHaveLength(1)
    expect(groupedActivities(wrapper)).toEqual([])
  })

  it('groups every camp comment by activity in programme order', () => {
    const wrapper = mountList({
      scope: null,
      scheduleEntries: [scheduleEntry(otherActivity), scheduleEntry(activity)],
      items: [comment(1), comment(2, { on: otherActivity }), comment(3)],
    })

    expect(groupedActivities(wrapper)).toEqual(['/activities/2', '/activities/1'])
    expect(renderedComments(wrapper)).toEqual([
      '/comments/2',
      '/comments/1',
      '/comments/3',
    ])
  })

  it('positions an activity by its first remaining schedule entry', () => {
    const wrapper = mountList({
      scope: null,
      scheduleEntries: [scheduleEntry(otherActivity), scheduleEntry(activity)],
      items: [comment(1), comment(2, { on: otherActivity })],
    })

    expect(groupedActivities(wrapper)).toEqual(['/activities/2', '/activities/1'])
  })

  it('names the activity of each group', () => {
    const wrapper = mountList({
      scope: null,
      items: [comment(1), comment(2, { on: otherActivity })],
    })

    expect(groupedActivities(wrapper)).toEqual(['/activities/1', '/activities/2'])
  })

  it('collects camp comments and orphans in their own buckets', () => {
    const wrapper = mountList({
      scope: null,
      scheduleEntries: [],
      items: [
        comment(1, { on: null }),
        comment(2, { on: null, orphanDescription: 'Postenarbeit' }),
        comment(3, { on: null }),
      ],
    })

    const groups = wrapper.findAll('.ec-comments-list__group')
    expect(groups.map((group) => group.find('h3').text())).toEqual([
      'components.comments.commentsList.campComments',
      'components.comments.commentsList.orphanedComments',
    ])
    expect(renderedComments(wrapper)).toEqual([
      '/comments/1',
      '/comments/3',
      '/comments/2',
    ])
  })

  it('shows camp comments first and orphans last', () => {
    const wrapper = mountList({
      scope: null,
      items: [
        comment(1),
        comment(2, { on: null, orphanDescription: 'Postenarbeit' }),
        comment(3, { on: null }),
      ],
    })

    expect(renderedComments(wrapper)).toEqual([
      '/comments/3',
      '/comments/1',
      '/comments/2',
    ])
  })

  it('marks a comment whose activity was deleted', () => {
    const wrapper = mountList({
      scope: null,
      items: [comment(1, { on: null, orphanDescription: 'Postenarbeit' })],
    })

    expect(wrapper.find('.ec-comment-card__activity').text()).toBe(
      'components.comments.commentCard.deletedActivity Postenarbeit'
    )
  })

  it('names no activity on the cards of an activity group', () => {
    const wrapper = mountList({ scope: null, items: [comment(1)] })

    expect(wrapper.find('.ec-comment-card__activity').exists()).toBe(false)
  })

  it('names no activity for a camp comment', () => {
    const wrapper = mountList({ scope: null, items: [comment(1, { on: null })] })

    expect(wrapper.find('.ec-comment-card__activity').exists()).toBe(false)
  })

  it('does not repeat the activity when the panel is scoped to it', () => {
    const wrapper = mountList({ items: [comment(1)] })

    expect(wrapper.find('.ec-comment-card__activity').exists()).toBe(false)
  })

  describe('focusing an activity', () => {
    function nextFrame() {
      return new Promise((resolve) => requestAnimationFrame(() => resolve()))
    }

    // focusActivityComments drops the focus and reassigns it two frames later, so that
    // the browser renders the group without the class and restarts its animation
    async function focus(activityUri) {
      Element.prototype.scrollIntoView = vi.fn()
      focusActivityComments(activityUri)
      await nextFrame()
      await nextFrame()
    }

    it('scrolls to and marks the group of the focused activity', async () => {
      const wrapper = mountList({
        scope: null,
        items: [comment(1), comment(2, { on: otherActivity })],
      })

      await focus(otherActivity._meta.self)

      expect(Element.prototype.scrollIntoView).toHaveBeenCalledOnce()
      expect(focusedGroupActivity(wrapper)).toBe(otherActivity._meta.self)
    })

    it('scrolls again when the same activity is focused twice', async () => {
      const wrapper = mountList({
        scope: null,
        items: [comment(1), comment(2, { on: otherActivity })],
      })

      await focus(otherActivity._meta.self)
      await focus(otherActivity._meta.self)

      expect(Element.prototype.scrollIntoView).toHaveBeenCalledOnce()
      expect(focusedGroupActivity(wrapper)).toBe(otherActivity._meta.self)
    })

    it('moves keyboard focus to the focused group', async () => {
      const wrapper = mountList({
        scope: null,
        items: [comment(1), comment(2, { on: otherActivity })],
        attachTo: document.body,
      })

      await focus(otherActivity._meta.self)

      const focused = wrapper.find('.ec-comments-list__group--focused')
      expect(focused.find('h3').element).toBe(document.activeElement)
    })

    it('keeps the group marked until another activity is focused', async () => {
      const wrapper = mountList({
        scope: null,
        items: [comment(1), comment(2, { on: otherActivity })],
      })

      await focus(activity._meta.self)
      expect(focusedGroupActivity(wrapper)).toBe(activity._meta.self)

      await focus(otherActivity._meta.self)
      expect(focusedGroupActivity(wrapper)).toBe(otherActivity._meta.self)
    })

    it('marks no group for an activity that has none', async () => {
      const wrapper = mountList({
        scope: null,
        scheduleEntries: [scheduleEntry(activity)],
        items: [comment(1)],
      })

      await focus(otherActivity._meta.self)

      expect(wrapper.find('.ec-comments-list__group--focused').exists()).toBe(false)
      expect(Element.prototype.scrollIntoView).not.toHaveBeenCalled()
    })

    it('does not scroll while the comments are still loading', async () => {
      mountList({ scope: null, items: [comment(1)], loading: true })

      await focus(activity._meta.self)

      expect(Element.prototype.scrollIntoView).not.toHaveBeenCalled()
    })
  })

  it('offers a delete button on own comments only', () => {
    const wrapper = mountList({
      items: [comment(1), comment(2, { authorSelf: '/users/somebody' })],
    })

    const cards = wrapper.findAllComponents({ name: 'CommentCard' })
    expect(cards[0].find('.ec-comment-card__delete').exists()).toBe(true)
    expect(cards[1].find('.ec-comment-card__delete').exists()).toBe(false)
  })
})
