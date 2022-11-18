import { screen, waitFor } from '@testing-library/vue'
import { render, setTestLocale, snapshotOf } from '@/test/renderWithVuetify.js'
import user from '@testing-library/user-event'
import EColorPicker from '../EColorPicker'

import { regex } from 'vee-validate/dist/rules'
import { extend } from 'vee-validate'
extend('regex', regex)

describe('An EColorPicker', () => {
  const COLOR1 = '#ff0000'
  const COLOR2 = '#ff00ff'
  const COLOR3 = '#FAFFAF'
  const INVALID_COLOR = 'some new color'
  const PICKER_BUTTON_LABEL_TEXT = 'Dialog öffnen um eine Farbe für test zu wählen'
  const VALIDATION_MESSAGE = 'test is not valid'

  beforeEach(() => {
    setTestLocale('de')
  })

  it('renders the component', async () => {
    // given

    // when
    render(EColorPicker, {
      props: {
        value: COLOR1,
        name: 'test',
      },
    })

    // then
    await screen.findByDisplayValue(COLOR1)
    screen.getByLabelText(PICKER_BUTTON_LABEL_TEXT)
  })

  it('looks like a color picker', async () => {
    // given

    // when
    const { container } = render(EColorPicker, {
      props: { value: COLOR1, name: 'test' },
    })

    // then
    expect(snapshotOf(container)).toMatchSnapshot('pickerclosed')

    // when
    await user.click(screen.getByLabelText(PICKER_BUTTON_LABEL_TEXT))

    // then
    await screen.findByText('Schliessen')
    expect(snapshotOf(container)).toMatchSnapshot('pickeropen')
  })

  it('updates v-model when the value changes', async () => {
    // given
    const { emitted } = render(EColorPicker, {
      props: { value: COLOR1, name: 'test' },
    })
    const inputField = await screen.findByDisplayValue(COLOR1)

    // when
    await user.clear(inputField)
    await user.keyboard(COLOR2)

    // then
    await waitFor(async () => {
      const events = emitted().input
      // some input events were fired
      expect(events.length).toBeGreaterThan(0)
      // the last one included the parsed version of our entered time
      expect(events[events.length - 1]).toEqual([COLOR2])
    })
    // Our entered time should be visible...
    screen.getByDisplayValue(COLOR2)
    // ...and stay visible
    return expect(
      waitFor(() => {
        expect(screen.getByDisplayValue(COLOR2)).not.toBeVisible()
      })
    ).rejects.toThrow(/Received element is visible/)
  })

  it('updates v-model when a new color is selected in the picker', async () => {
    // given
    const { emitted, container } = render(EColorPicker, {
      props: { value: COLOR1, name: 'test' },
    })
    await screen.findByDisplayValue(COLOR1)

    // when
    // click the button to open the picker
    await user.click(screen.getByLabelText(PICKER_BUTTON_LABEL_TEXT))
    // click inside the color picker canvas to select a different color
    const canvas = container.querySelector('canvas')
    await user.click(canvas, { clientX: 10, clientY: 10 })
    // click the close button
    await user.click(screen.getByText('Schliessen'))

    // then
    await waitFor(async () => {
      const events = emitted().input
      // some input events were fired
      expect(events.length).toBeGreaterThan(0)
      // the last one included the parsed version of our entered time
      expect(events[events.length - 1]).toEqual([COLOR3])
    })
    // Our entered time should be visible...
    screen.getByDisplayValue(COLOR3)
    // ...and stay visible
    return expect(
      waitFor(() => {
        expect(screen.getByDisplayValue(COLOR3)).not.toBeVisible()
      })
    ).rejects.toThrow(/Received element is visible/)
  })

  it('validates the input', async () => {
    // given
    render(EColorPicker, {
      props: { value: COLOR1, name: 'test' },
    })
    const inputField = await screen.findByDisplayValue(COLOR1)

    // when
    await user.clear(inputField)
    await user.keyboard(INVALID_COLOR)

    // then
    await screen.findByText(VALIDATION_MESSAGE)
  })

  it('accepts 3-digit hex color codes, after picker has been shown', async () => {
    render(EColorPicker, {
      props: { value: COLOR1, name: 'test' },
    })
    const inputField = await screen.findByDisplayValue(COLOR1)
    // click the button to open the picker
    await user.click(screen.getByLabelText(PICKER_BUTTON_LABEL_TEXT))

    // when
    await user.clear(inputField)
    await user.keyboard('#abc')

    // then
    await waitFor(() => {
      screen.getByDisplayValue('#AABBCC')
    })
  })
})
