import { describe, expect, it } from 'vitest'
import { mount } from '@vue/test-utils'
import { setupVuetify } from '/tests/setupVuetify.js'
import CommentsList from '@/components/comments/CommentsList.vue'

setupVuetify()

const currentUser = { _meta: { self: '/users/me' } }
const camp = { _meta: { self: '/camps/1' } }
const activity = { _meta: { self: '/activities/1', load: Promise.resolve() } }
const otherActivity = { _meta: { self: '/activities/2', load: Promise.resolve() } }

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

function mountList({ loading = false, items = [], scope = activity } = {}) {
  return mount(CommentsList, {
    props: { camp, activity: scope },
    global: {
      mocks: {
        $t: (key, named) => [key, ...Object.values(named ?? {})].join(' '),
        $date: (value) => ({ format: () => value }),
        $store: { getters: { getLoggedInUser: currentUser } },
        api: { get: () => ({ comments: () => ({ _meta: { loading }, items }) }) },
      },
      stubs: {
        CommentComposer: true,
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

describe('CommentsList', () => {
  it('shows skeletons while the collection is loading', () => {
    const wrapper = mountList({ loading: true })

    expect(wrapper.findAll('.v-skeleton-loader')).toHaveLength(2)
    expect(wrapper.findAllComponents({ name: 'CommentCard' })).toHaveLength(0)
  })

  it('shows an empty state when the scope has no comments', () => {
    const wrapper = mountList({ items: [comment(1, { on: otherActivity })] })

    expect(wrapper.text()).toContain('components.comments.commentsList.empty')
    expect(wrapper.findAllComponents({ name: 'CommentCard' })).toHaveLength(0)
  })

  it('keeps the order the API returned', () => {
    const wrapper = mountList({ items: [comment(1), comment(2), comment(3)] })

    expect(
      wrapper
        .findAllComponents({ name: 'CommentCard' })
        .map((c) => c.props().comment._meta.self)
    ).toEqual(['/comments/1', '/comments/2', '/comments/3'])
  })

  it('shows only the comments of the scoped activity', () => {
    const wrapper = mountList({
      items: [comment(1), comment(2, { on: otherActivity }), comment(3, { on: null })],
    })

    expect(
      wrapper
        .findAllComponents({ name: 'CommentCard' })
        .map((c) => c.props().comment._meta.self)
    ).toEqual(['/comments/1'])
  })

  it('shows every camp comment when there is no activity scope', () => {
    const wrapper = mountList({
      scope: null,
      items: [comment(1), comment(2, { on: otherActivity }), comment(3, { on: null })],
    })

    expect(wrapper.findAllComponents({ name: 'CommentCard' })).toHaveLength(3)
  })

  it('links to the activity of each comment when there is no activity scope', () => {
    const wrapper = mountList({
      scope: null,
      items: [comment(1), comment(2, { on: otherActivity })],
    })

    const links = wrapper.findAllComponents({ name: 'ScheduleEntryLinks' })
    expect(links.map((link) => link.props().activityPromise)).toEqual([
      activity._meta.load,
      otherActivity._meta.load,
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

  it('names no activity for a camp comment', () => {
    const wrapper = mountList({ scope: null, items: [comment(1, { on: null })] })

    expect(wrapper.find('.ec-comment-card__activity').exists()).toBe(false)
  })

  it('does not repeat the activity when the panel is scoped to it', () => {
    const wrapper = mountList({ items: [comment(1)] })

    expect(wrapper.find('.ec-comment-card__activity').exists()).toBe(false)
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
