import { beforeEach, describe, expect, it, vi } from 'vitest'
import { mount } from '@vue/test-utils'
import { setupVuetify } from '/tests/setupVuetify.js'
import ActivityRow from '@/components/dashboard/ActivityRow.vue'
import { commentsState } from '@/components/comments/commentsState.js'

setupVuetify()

vi.mock('@/router.js', () => ({ scheduleEntryRoute: () => '/somewhere' }))

const activity = { _meta: { self: '/activities/1' } }

let push

function resize(width) {
  window.innerWidth = width
  window.dispatchEvent(new Event('resize'))
}

const scheduleEntry = {
  _meta: { self: '/schedule_entries/1', loading: false },
  number: '1.1',
  start: '2026-08-01T08:00:00+00:00',
  end: '2026-08-01T09:00:00+00:00',
  activity: () => ({
    ...activity,
    title: 'Lagerbau',
    location: '',
    category: () => ({ short: 'LS', color: '#ff0000', numberingStyle: '1' }),
    activityResponsibles: () => ({ items: [] }),
    progressLabel: () => ({ title: 'Geplant' }),
  }),
}

function mountRow(commentCount = 0, { mobile = false } = {}) {
  push = vi.fn()
  resize(mobile ? 375 : 1280)
  return mount(ActivityRow, {
    props: {
      scheduleEntry,
      loadingEndpoints: {
        categories: false,
        periods: false,
        campCollaborations: false,
        progressLabels: false,
      },
      commentCount,
    },
    global: {
      mocks: {
        $t: (key, named, count) => [key, count].filter((v) => v !== undefined).join(' '),
        $tc: (key) => key,
        $router: { push: (...args) => push(...args) },
      },
      stubs: { CategoryChip: true, AvatarRow: true, RouterLink: true },
    },
  })
}

describe('ActivityRow', () => {
  beforeEach(() => {
    commentsState.open = false
    commentsState.activityFilter = null
    resize(1280)
  })

  it('shows no comment badge for an activity without comments', () => {
    expect(mountRow(0).find('[data-testid="activity-comment-count"]').exists()).toBe(
      false
    )
  })

  it('shows the number of comments', () => {
    const badge = mountRow(3).find('[data-testid="activity-comment-count"]')

    expect(badge.exists()).toBe(true)
    expect(badge.text()).toContain('3')
    expect(badge.attributes('aria-label')).toBe(
      'components.dashboard.activityRow.comments 3'
    )
  })

  it('navigates to the activity and opens the panel on desktop', async () => {
    await mountRow(1).find('[data-testid="activity-comment-count"]').trigger('click')

    expect(push).toHaveBeenCalledWith('/somewhere')
    await vi.waitFor(() => expect(commentsState.open).toBe(true))
    expect(commentsState.activityFilter).toBe(null)
  })

  it('opens the panel filtered to the activity, without navigating, on mobile', async () => {
    const wrapper = mountRow(1, { mobile: true })

    await wrapper.find('[data-testid="activity-comment-count"]').trigger('click')

    expect(commentsState.open).toBe(true)
    expect(commentsState.activityFilter._meta.self).toBe('/activities/1')
    expect(push).not.toHaveBeenCalled()
  })
})
