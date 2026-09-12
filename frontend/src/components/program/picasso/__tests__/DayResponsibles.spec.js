import { describe, expect, it, vi } from 'vitest'
import { reactive } from 'vue'
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

// days() switches from initialDays to reloadedDays once $reload() is called
function createPeriod({ initialDays, reloadedDays }) {
  const days = reactive({ items: initialDays })
  days.$reload = vi.fn().mockImplementation(() => {
    days.items = reloadedDays
    return Promise.resolve()
  })

  return {
    _meta: { self: '/periods/1' },
    camp: () => ({
      campCollaborations: () => ({ items: [], _meta: { load: Promise.resolve() } }),
    }),
    days: () => days,
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
  it('reloads days and resolves the current day only once fresh data arrived', async () => {
    const staleDay = makeDay('2020-06-01T00:00:00+00:00')
    const freshDay = makeDay('2026-01-01T00:00:00+00:00')
    const period = createPeriod({
      initialDays: [staleDay],
      reloadedDays: [freshDay],
    })

    const wrapper = mountDayResponsibles({ period, date: '2026-01-01T00:00:00+00:00' })

    // reload is still in flight right after mount
    expect(wrapper.vm.isLoading).toBe(true)

    await flushPromises()

    expect(period.days().$reload).toHaveBeenCalledTimes(1)
    expect(wrapper.vm.isLoading).toBe(false)
    // compare via self link: props are wrapped in a reactive proxy
    expect(wrapper.vm.day._meta.self).toBe(freshDay._meta.self)
    expect(wrapper.vm.dayResponsibles).toBeDefined()
  })

  it('stops loading instead of hanging forever when no day matches even after reload', async () => {
    const staleDay = makeDay('2020-06-01T00:00:00+00:00')
    const period = createPeriod({
      initialDays: [staleDay],
      reloadedDays: [], // still no matching day after reload
    })

    const wrapper = mountDayResponsibles({ period, date: '2026-01-01T00:00:00+00:00' })

    await flushPromises()

    expect(wrapper.vm.isLoading).toBe(false)
    expect(wrapper.vm.day).toBeUndefined()
    expect(wrapper.vm.dayResponsibles).toBeUndefined()
  })
})
