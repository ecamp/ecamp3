import { describe, it, expect, vi } from 'vitest'
import { mount } from '@vue/test-utils'
import CampListItem from '../CampListItem.vue'

// Mock dependencies
vi.mock('@/router.js', () => ({
  campRoute: vi.fn((camp) => `/camps/${camp.id}`),
}))

describe('CampListItem.vue - Date Display', () => {
  const defaultCamp = {
    id: 1,
    title: 'Sommerlager 2026',
    motto: 'Verflixte Zeitreisen',
    organizer: 'Doctor Who',
    isShared: false,
  }

  const mountComponent = (props = {}) => {
    return mount(CampListItem, {
      props: {
        camp: defaultCamp,
        ...props,
      },
      global: {
        stubs: {
          'v-list-item': {
            template: '<div class="v-list-item" :data-to="to"><slot /></div>',
            props: ['to'],
          },
          'v-list-item-title': {
            template: '<div class="v-list-item-title"><slot /></div>',
          },
          'v-list-item-subtitle': {
            template: '<div class="v-list-item-subtitle"><slot /></div>',
          },
          'v-chip': { template: '<span class="v-chip"><slot /></span>' },
        },
        mocks: {
          $t: (msg) => msg,
          $store: {
            state: {
              lang: {
                language: 'en-US',
              },
            },
          },
        },
      },
    })
      .get('.ec-camp-list-item-date')
      .text()
      .replace(/\s+/g, ' ')
      .trim()
  }

  it('renders nothing for the date if no periods are provided', () => {
    const listItemDate = mountComponent({ periods: [] })

    expect(listItemDate).toBe('')
  })

  it('calculates and formats a single period within the same year correctly', () => {
    const listItemDate = mountComponent({
      periods: [{ start: '2026-07-15T00:00:00Z', end: '2026-07-22T00:00:00Z' }],
    })

    expect(listItemDate).toBe('Jul 2026')
  })

  it('calculates and formats multiple periods within the same year correctly', () => {
    const listItemDate = mountComponent({
      periods: [
        { start: '2026-07-15T00:00:00Z', end: '2026-07-22T00:00:00Z' },
        { start: '2026-08-10T00:00:00Z', end: '2026-08-15T00:00:00Z' },
      ],
    })

    expect(listItemDate).toBe('Jul, Aug 2026')
  })

  it('calculates and formats cross-year periods correctly', () => {
    const listItemDate = mountComponent({
      periods: [{ start: '2025-12-28T00:00:00Z', end: '2026-01-04T00:00:00Z' }],
    })

    expect(listItemDate).toBe('Dec 2025 – Jan 2026')
  })

  it('groups and separates same-year and cross-year periods with a pipe', () => {
    const listItemDate = mountComponent({
      periods: [
        { start: '2025-12-28T00:00:00Z', end: '2026-01-04T00:00:00Z' },
        { start: '2026-07-15T00:00:00Z', end: '2026-07-22T00:00:00Z' },
      ],
    })

    expect(listItemDate).toBe('Jul 2026 | Dec 2025 – Jan 2026')
  })
})
