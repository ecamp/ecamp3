import { describe, expect, it, vi } from 'vitest'
import { mount } from '@vue/test-utils'
import { setupVuetify } from '/tests/setupVuetify.js'
import CampPeriodsListItem from '../CampPeriodsListItem.vue'
import DialogPeriodDateEdit from '../DialogPeriodDateEdit.vue'

setupVuetify()

function createPeriod() {
  const reloads = {
    days: vi.fn(),
    dayResponsibles: vi.fn(),
    scheduleEntries: vi.fn(),
  }

  const period = {
    _meta: { self: '/periods/1', loading: false },
    description: 'Hauptlager',
    start: '2026-06-01T00:00:00+00:00',
    end: '2026-06-10T00:00:00+00:00',
    camp: () => ({ periods: () => ({ items: [{}, {}] }) }),
    days: () => ({ $reload: reloads.days }),
    dayResponsibles: () => ({ $reload: reloads.dayResponsibles }),
    scheduleEntries: () => ({ $reload: reloads.scheduleEntries }),
  }

  return { period, reloads }
}

function mountListItem(period) {
  return mount(CampPeriodsListItem, {
    props: { period },
    global: {
      mocks: { $t: (key) => key },
      stubs: {
        // explicit stub: successHandler comes from the extends: DialogBase mixin
        DialogPeriodDateEdit: {
          name: 'DialogPeriodDateEdit',
          props: ['period', 'mode', 'successHandler'],
          template: '<div />',
        },
        DialogPeriodDescriptionEdit: true,
        DialogEntityDelete: true,
        // VMenu needs browser overlay APIs jsdom doesn't provide
        VMenu: { template: '<div><slot name="activator" :props="{}" /><slot /></div>' },
      },
    },
  })
}

describe('CampPeriodsListItem', () => {
  it('reloads days, dayResponsibles and scheduleEntries after a period date change', () => {
    const { period, reloads } = createPeriod()
    const wrapper = mountListItem(period)

    wrapper.vm.reloadPeriodDateDependents()

    expect(reloads.days).toHaveBeenCalledTimes(1)
    expect(reloads.dayResponsibles).toHaveBeenCalledTimes(1)
    expect(reloads.scheduleEntries).toHaveBeenCalledTimes(1)
  })

  it('wires reloadPeriodDateDependents as the success handler of all three period date dialogs', () => {
    const { period } = createPeriod()
    const wrapper = mountListItem(period)

    const dateEditDialogs = wrapper.findAllComponents(DialogPeriodDateEdit)

    expect(dateEditDialogs).toHaveLength(3)
    expect(dateEditDialogs.map((dialog) => dialog.props('mode'))).toEqual([
      'move',
      'changeStart',
      'changeEnd',
    ])
    dateEditDialogs.forEach((dialog) => {
      expect(dialog.props('successHandler')).toBe(wrapper.vm.reloadPeriodDateDependents)
    })
  })
})
