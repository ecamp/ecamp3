import { describe, expect, it, vi } from 'vitest'
import { mount } from '@vue/test-utils'
import flushPromises from 'flush-promises'
import { setupVuetify } from '/tests/setupVuetify.js'
import DayResponsibles from '../DayResponsibles.vue'
import dayjs from '@/common/helpers/dayjs.js'

setupVuetify()

function makeDay(start, dayResponsibleItems = []) {
  return {
    _meta: { self: `/days/${start}` },
    start,
    dayResponsibles: () => ({
      items: dayResponsibleItems,
      _meta: { load: Promise.resolve() },
    }),
  }
}

function createPeriod({ days }) {
  const daysCollection = {
    items: days,
    $reload: vi.fn().mockResolvedValue(),
  }

  return {
    _meta: { self: '/periods/1' },
    camp: () => ({
      campCollaborations: () => ({ items: [], _meta: { load: Promise.resolve() } }),
    }),
    days: () => daysCollection,
  }
}

function mountDayResponsibles({ period, date }) {
  return mount(DayResponsibles, {
    props: { period, date },
    global: {
      mocks: {
        $t: (key) => key,
        $date: dayjs,
      },
      stubs: {
        ESelect: true,
      },
    },
  })
}

describe('DayResponsibles', () => {
  it('reloads days before reading dayResponsibles', async () => {
    const day = makeDay('2026-01-01T00:00:00+00:00')
    const period = createPeriod({ days: [day] })

    const wrapper = mountDayResponsibles({ period, date: '2026-01-01T00:00:00+00:00' })

    // reload is still in flight right after mount
    expect(wrapper.vm.isLoading).toBe(true)

    await flushPromises()

    expect(period.days().$reload).toHaveBeenCalledTimes(1)
    expect(wrapper.vm.isLoading).toBe(false)
  })
})
