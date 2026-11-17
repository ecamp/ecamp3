import { describe, it, expect, vi, afterEach } from 'vitest'
import { mount } from '@vue/test-utils'
import CampHitobitoSync from '../CampHitobitoSync.vue'

vi.mock('@/components/layout/ContentGroup.vue', () => ({
  default: { template: '<div><slot /></div>' },
}))
vi.mock('@/components/buttons/ButtonEdit.vue', () => ({
  default: { template: '<button><slot /></button>' },
}))
vi.mock('@/router.js', () => ({
  campRoute: vi.fn((camp, subroute) => `/camps/${camp.id}/${subroute}`),
}))

describe('CampHitobitoSync.vue', () => {
  const camp = { id: 'abc', hitobitoProvider: 'pbsmidata', hitobitoEventId: '123' }

  const mountComponent = () =>
    mount(CampHitobitoSync, {
      props: { camp },
      global: {
        stubs: {
          'v-list': { template: '<div><slot /></div>' },
          'v-list-item': { template: '<div><slot /><slot name="append" /></div>' },
          'v-list-item-title': { template: '<div><slot /></div>' },
          'v-list-item-subtitle': { template: '<div><slot /></div>' },
          'v-icon': true,
        },
        mocks: { $t: (msg) => msg },
      },
    })

  afterEach(() => {
    delete window.environment
  })

  it('links to the event in Hitobito, opening in a new tab', () => {
    window.environment = { HITOBITO_PBSMIDATA_BASE_URL: 'https://db.scout.ch' }

    const link = mountComponent().find('[data-testid="hitobito-event-link"]')

    expect(link.attributes('href')).toBe('https://db.scout.ch/events/123')
    expect(link.attributes('target')).toBe('_blank')
    expect(link.attributes('rel')).toBe('noopener noreferrer')
  })
})
