import { fireEvent, screen, waitFor } from '@testing-library/vue'
import { render, setTestLocale, snapshotOf } from '@/test/renderWithVuetify.js'
import user from '@testing-library/user-event'
import EColorPicker from '../EColorPicker.vue'

import { regex } from 'vee-validate/dist/rules'
import { extend } from 'vee-validate'
extend('regex', regex)

describe('An EColorPicker', () => {
  const COLOR1 = '#FF0000'
  const COLOR2 = '#FF00FF'
  const COLOR3 = '#FAFFAF'
  const INVALID_COLOR = 'some new color'
  const PICKER_BUTTON_LABEL_TEXT = 'Dialog öffnen, um eine Farbe für test zu wählen'
  const VALIDATION_MESSAGE = 'Bitte gültige Farbe eingeben.'

  beforeEach(() => {
    setTestLocale('de')
  })

  it('renders the component', async () => {
    // given

    // when
    render(EColorPicker, {
      props: {
        value: COLOR1,
        label: 'test',
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
      props: { value: COLOR1, label: 'test' },
    })

    // then
    expect(snapshotOf(container)).toMatchSnapshot('pickerclosed')

    // when
    await user.click(screen.getByLabelText(PICKER_BUTTON_LABEL_TEXT))

    // then
    await screen.findByTestId('colorpicker')
    expect(snapshotOf(container)).toMatchSnapshot('pickeropen')
  })

  it('opens the picker when the text field is clicked', async () => {
    // given
    render(EColorPicker, {
      props: { value: COLOR1, label: 'test' },
    })
    const inputField = await screen.findByDisplayValue(COLOR1)

    // when
    await user.click(inputField)

    // then
    await waitFor(async () => {
      expect(await screen.findByTestId('colorpicker')).toBeVisible()
    })
  })

  it('opens the picker when the icon button is clicked', async () => {
    // given
    render(EColorPicker, {
      props: { value: COLOR1, label: 'test' },
    })
    const button = await screen.getByLabelText(PICKER_BUTTON_LABEL_TEXT)

    // when
    await user.click(button)

    // then
    await waitFor(async () => {
      expect(await screen.findByTestId('colorpicker')).toBeVisible()
    })
  })

  it('closes the picker when clicking outside', async () => {
    // given
    render(EColorPicker, {
      props: { value: COLOR1, label: 'test' },
    })
    const button = await screen.getByLabelText(PICKER_BUTTON_LABEL_TEXT)
    await user.click(button)
    await waitFor(async () => {
      expect(await screen.findByTestId('colorpicker')).toBeVisible()
    })

    // when
    await user.click(document.body)

    // then
    await waitFor(async () => {
      expect(await screen.queryByTestId('colorpicker')).not.toBeVisible()
    })
  })

  it('closes the picker when pressing escape', async () => {
    // given
    render(EColorPicker, {
      props: { value: COLOR1, label: 'test' },
    })
    const button = await screen.getByLabelText(PICKER_BUTTON_LABEL_TEXT)
    await user.click(button)
    await waitFor(async () => {
      expect(await screen.findByTestId('colorpicker')).toBeVisible()
    })

    // when
    await user.keyboard('{Escape}')

    // then
    await waitFor(async () => {
      expect(await screen.queryByTestId('colorpicker')).not.toBeVisible()
    })
  })

  it('does not close the picker when selecting a color', async () => {
    // given
    const { container } = render(EColorPicker, {
      props: { value: COLOR1, label: 'test' },
    })
    const button = await screen.getByLabelText(PICKER_BUTTON_LABEL_TEXT)
    await user.click(button)
    await waitFor(async () => {
      expect(await screen.findByTestId('colorpicker')).toBeVisible()
    })

    // when
    // click inside the color picker canvas to select a different color
    const canvas = container.querySelector('canvas')
    await user.click(canvas, { clientX: 10, clientY: 10 })

    // then
    // close button should stay visible
    await expect(
      waitFor(() => {
        expect(screen.queryByTestId('colorpicker')).not.toBeVisible()
      })
    ).rejects.toThrow(/Received element is visible/)
  })

  it('updates v-model when the value changes', async () => {
    // given
    const { emitted } = render(EColorPicker, {
      props: { value: COLOR1, label: 'test' },
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
    await expect(
      waitFor(() => {
        expect(screen.getByDisplayValue(COLOR2)).not.toBeVisible()
      })
    ).rejects.toThrow(/Received element is visible/)
  })

  it('updates v-model when a new color is selected in the picker', async () => {
    // given
    const { emitted, container } = render(EColorPicker, {
      props: { value: COLOR1, label: 'test' },
    })
    await screen.findByDisplayValue(COLOR1)
    const button = await screen.getByLabelText(PICKER_BUTTON_LABEL_TEXT)

    // when
    // click the button to open the picker
    await user.click(button)
    // click inside the color picker canvas to select a different color
    const canvas = container.querySelector('canvas')
    await user.click(canvas, { clientX: 10, clientY: 10 })
    // click the close button
    await user.click(screen.getByTestId('colorpicker'))

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
    await expect(
      waitFor(() => {
        expect(screen.getByDisplayValue(COLOR3)).not.toBeVisible()
      })
    ).rejects.toThrow(/Received element is visible/)
  })

  it('validates the input', async () => {
    // given
    render(EColorPicker, {
      props: { value: COLOR1, path: 'test', validationLabelOverride: 'test' },
    })
    const inputField = await screen.findByDisplayValue(COLOR1)

    // when
    await user.clear(inputField)
    await user.keyboard(INVALID_COLOR)
    await fireEvent.blur(inputField)

    // then
    await screen.findByText(VALIDATION_MESSAGE)
  })

  it('accepts 3-digit hex color codes, after picker has been shown', async () => {
    render(EColorPicker, {
      props: { value: COLOR1, label: 'test' },
    })
    const inputField = await screen.findByDisplayValue(COLOR1)
    const button = await screen.getByLabelText(PICKER_BUTTON_LABEL_TEXT)
    // click the button to open the picker
    await user.click(button)

    // when
    await user.clear(inputField)
    await user.keyboard('#abc')
    await fireEvent.blur(inputField)

    // then
    await waitFor(() => {
      screen.getByDisplayValue('#AABBCC')
    })
  })

  it('accepts null', async () => {
    render(EColorPicker, {
      props: { value: COLOR2, label: 'test' },
    })
    const inputField = await screen.findByDisplayValue(COLOR2)
    expect(inputField).toHaveValue(COLOR2)
    const button = await screen.getByLabelText(PICKER_BUTTON_LABEL_TEXT)
    // click the button to open the picker
    await user.click(button)

    // when
    await user.click(screen.getByTestId('colorpicker').querySelector('.reset'))
    expect(inputField).toHaveValue('')
  })
})
