import { describe, expect, it, vi } from 'vitest'
import { shallowMount } from '@vue/test-utils'
import flushPromises from 'flush-promises'
import dayjs from '@/common/helpers/dayjs.js'
import DialogPeriodDateEdit from '@/components/campAdmin/DialogPeriodDateEdit.vue'

function createPeriod() {
  const scheduleEntries = { _meta: { self: '/schedule_entries?period=/periods/1' } }
  const days = { _meta: { self: '/days?period=/periods/1' } }
  const dayResponsibles = { _meta: { self: '/day_responsibles?day.period=/periods/1' } }
  return {
    _meta: { self: '/periods/1' },
    description: 'Hauptlager',
    start: '2024-07-01',
    end: '2024-07-07',
    scheduleEntries: () => scheduleEntries,
    days: () => days,
    dayResponsibles: () => dayResponsibles,
  }
}

async function mountDialog({
  mode = 'move',
  patch = vi.fn().mockResolvedValue({}),
} = {}) {
  const period = createPeriod()
  const api = {
    get: () => ({
      _meta: {
        load: Promise.resolve({
          start: period.start,
          end: period.end,
          moveScheduleEntries: false,
        }),
      },
    }),
    patch,
    reload: vi.fn().mockResolvedValue({}),
  }
  const wrapper = shallowMount(DialogPeriodDateEdit, {
    props: { period, mode },
    global: {
      mocks: { $t: (key) => key, $date: dayjs, api },
      stubs: {
        DialogForm: {
          props: ['submitAction'],
          template: '<button data-testid="submit" @click="submitAction" />',
        },
        EForm: true,
        EDatePicker: true,
        ETextField: true,
      },
    },
  })
  wrapper.vm.open()
  await flushPromises()
  return { wrapper, period, api }
}

async function submit(wrapper) {
  await wrapper.find('[data-testid="submit"]').trigger('click')
  await flushPromises()
}

describe('DialogPeriodDateEdit', () => {
  // The dates of schedule entries and days are derived from the period start on the server,
  // so the cached collections are stale after the period dates changed (#5283).
  it.each([['move'], ['changeStart'], ['changeEnd']])(
    'reloads the schedule entries, days and day responsibles of the period after saving in mode %s',
    async (mode) => {
      const { wrapper, period, api } = await mountDialog({ mode })

      await submit(wrapper)

      expect(api.patch).toHaveBeenCalledOnce()
      expect(api.reload).toHaveBeenCalledWith(period.scheduleEntries())
      expect(api.reload).toHaveBeenCalledWith(period.days())
      expect(api.reload).toHaveBeenCalledWith(period.dayResponsibles())
    }
  )

  it('closes the dialog after saving', async () => {
    const { wrapper } = await mountDialog()

    await submit(wrapper)

    expect(wrapper.vm.showDialog).toBe(false)
    expect(wrapper.emitted('success')).toHaveLength(1)
  })

  it('does not reload anything when saving fails', async () => {
    const { wrapper, api } = await mountDialog({
      patch: vi.fn().mockRejectedValue(new Error('nope')),
    })

    await submit(wrapper)

    expect(api.reload).not.toHaveBeenCalled()
    expect(wrapper.vm.showDialog).toBe(true)
  })
})
