import { beforeEach, describe, expect, it, vi } from 'vitest'
import { mount } from '@vue/test-utils'
import { setupVuetify } from '/tests/setupVuetify.js'
import ActivityRow from '@/components/dashboard/ActivityRow.vue'
import { commentsState } from '@/components/comments/commentsState.js'

setupVuetify()

vi.mock('@/router.js', () => ({ scheduleEntryRoute: () => '/somewhere' }))

const activity = { _meta: { self: '/activities/1' } }

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

function mountRow(commentCount = 0) {
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
      },
      stubs: { CategoryChip: true, AvatarRow: true, RouterLink: true },
    },
  })
}

describe('ActivityRow', () => {
  beforeEach(() => {
    commentsState.open = false
    commentsState.focusedActivity = null
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

  it('opens the comments panel on the activity when clicked', async () => {
    await mountRow(1).find('[data-testid="activity-comment-count"]').trigger('click')

    expect(commentsState.open).toBe(true)
    await vi.waitFor(() => expect(commentsState.focusedActivity).toBe('/activities/1'))
  })
})
