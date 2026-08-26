import { afterEach, describe, expect, it, vi } from 'vitest'
import { enableAutoUnmount, mount } from '@vue/test-utils'
import { setupVuetify } from '/tests/setupVuetify.js'
import CommentsList from '@/components/comments/CommentsList.vue'

setupVuetify()
enableAutoUnmount(afterEach)

afterEach(() => {
  vi.restoreAllMocks()
})

const currentUser = { _meta: { self: '/users/me' } }

function activityFixture(uri) {
  return { _meta: { self: uri, load: Object.assign(Promise.resolve(), { uri }) } }
}

const activity = activityFixture('/activities/1')

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

function mountList({ loading = false, comments = [], showContext = false } = {}) {
  return mount(CommentsList, {
    props: { comments, loading, showContext },
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
    .map((card) => card.props().comment._meta.self)
}

function labelledActivities(wrapper) {
  return wrapper
    .findAllComponents({ name: 'ScheduleEntryLinks' })
    .map((link) => link.props().activityPromise.uri)
}

describe('CommentsList', () => {
  it('shows skeletons while the collection is loading', () => {
    const wrapper = mountList({ loading: true, comments: [comment(1)] })

    expect(wrapper.findAll('.v-skeleton-loader').length).toBe(2)
    expect(renderedComments(wrapper)).toEqual([])
  })

  it('shows an empty state when there are no comments', () => {
    const wrapper = mountList()

    expect(wrapper.text()).toContain('components.comments.commentsList.empty')
  })

  it('renders one card per comment, in the order it was given', () => {
    const wrapper = mountList({ comments: [comment(2), comment(1), comment(3)] })

    expect(renderedComments(wrapper)).toEqual([
      '/comments/2',
      '/comments/1',
      '/comments/3',
    ])
  })

  it('names the activity of each comment when showing context', () => {
    const wrapper = mountList({ comments: [comment(1)], showContext: true })

    expect(labelledActivities(wrapper)).toEqual(['/activities/1'])
  })

  it('labels a camp-level comment explicitly when showing context', () => {
    const wrapper = mountList({ comments: [comment(1, { on: null })], showContext: true })

    expect(wrapper.text()).toContain('components.comments.commentCard.campComment')
  })

  it('names no context when not showing context', () => {
    const wrapper = mountList({ comments: [comment(1), comment(2, { on: null })] })

    expect(labelledActivities(wrapper)).toEqual([])
    expect(wrapper.text()).not.toContain('components.comments.commentCard.campComment')
  })

  it('names the deleted activity of an orphan even without context', () => {
    const wrapper = mountList({
      comments: [comment(1, { on: null, orphanDescription: 'LS Sportolympiade' })],
    })

    expect(wrapper.text()).toContain(
      'components.comments.commentCard.deletedActivity LS Sportolympiade'
    )
  })

  it('offers a delete button on own comments only', () => {
    const wrapper = mountList({
      comments: [comment(1), comment(2, { authorSelf: '/users/other' })],
    })

    const cards = wrapper.findAllComponents({ name: 'CommentCard' })
    expect(cards[0].find('.ec-comment-card__delete').exists()).toBe(true)
    expect(cards[1].find('.ec-comment-card__delete').exists()).toBe(false)
  })

  it('scrolls its container smoothly to the bottom on request', () => {
    const wrapper = mountList({ comments: [comment(1)] })
    Object.defineProperty(wrapper.element, 'scrollHeight', { value: 640 })
    const scrollTo = vi.fn()
    wrapper.element.scrollTo = scrollTo

    wrapper.vm.scrollToBottom()

    expect(scrollTo).toHaveBeenCalledWith({ top: 640, behavior: 'smooth' })
  })

  it('jumps to the bottom when the reader asked for reduced motion', () => {
    const wrapper = mountList({ comments: [comment(1)] })
    Object.defineProperty(wrapper.element, 'scrollHeight', { value: 640 })
    const scrollTo = vi.fn()
    wrapper.element.scrollTo = scrollTo
    vi.spyOn(window, 'matchMedia').mockReturnValue({ matches: true })

    wrapper.vm.scrollToBottom()

    expect(scrollTo).toHaveBeenCalledWith({ top: 640, behavior: 'auto' })
  })
})
