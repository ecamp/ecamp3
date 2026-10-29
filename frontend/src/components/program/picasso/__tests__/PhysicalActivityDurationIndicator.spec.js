import { describe, expect, it } from 'vitest'
import { shallowMount } from '@vue/test-utils'
import PhysicalActivityDurationIndicator from '../PhysicalActivityDurationIndicator.vue'
import { ONE_MINUTE_IN_MILLISECONDS } from '@/helpers/vCalendarDragAndDrop.js'

function mountIndicator(props = {}) {
  return shallowMount(PhysicalActivityDurationIndicator, {
    props: {
      duration: '30 min',
      minutes: 30,
      entryDuration: 60 * ONE_MINUTE_IN_MILLISECONDS,
      ...props,
    },
    global: {
      mocks: { $t: (key) => key },
    },
  })
}

describe('PhysicalActivityDurationIndicator', () => {
  it.each([
    [30, 60, 0.5],
    [60, 60, 1],
    [90, 60, 1],
    [120, 180, 1],
    [0, 60, 0],
    [30, 0, 0],
  ])(
    'shows %s minutes in a %s minute entry as fraction %s',
    (minutes, duration, fraction) => {
      const wrapper = mountIndicator({
        minutes,
        entryDuration: duration * ONE_MINUTE_IN_MILLISECONDS,
        visible: true,
      })

      expect(
        wrapper.get('.e-physical-activity-duration__line').element.style.height
      ).toBe(`${fraction * 100}%`)
    }
  )

  it('collapses the line and hides the text when toggled off', async () => {
    const wrapper = mountIndicator()
    const line = wrapper.get('.e-physical-activity-duration__line')
    const text = wrapper.get('.e-physical-activity-duration__text')

    expect(line.element.style.height).toBe('0%')
    expect(text.attributes('aria-hidden')).toBe('true')

    await wrapper.setProps({ visible: true })

    expect(line.element.style.height).toBe('50%')
    expect(text.attributes('aria-hidden')).toBe('false')
    expect(text.text()).toBe('30 min')

    await wrapper.setProps({ visible: false })

    expect(line.element.style.height).toBe('0%')
    expect(text.attributes('aria-hidden')).toBe('true')
  })
})
