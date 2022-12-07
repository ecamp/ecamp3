import { screen, waitFor } from '@testing-library/vue'
import { render, setTestLocale, snapshotOf } from '@/test/renderWithVuetify.js'
import user from '@testing-library/user-event'
import ETimePicker from '../ETimePicker'

describe('An ETimePicker', () => {
  const TIME1_ISO = '2037-07-18T09:52:00+00:00'
  const TIME1_HHMM = '09:52'
  const TIME2_ISO = '2037-07-18T18:33:00+00:00'
  const TIME3_ISO = '2037-07-18T00:52:00+00:00'

  const localeData = {
    de: {
      time1: '09:52',
      time2: '18:33',
      time3: '00:52',
      firstHour: '0',
      labelText: 'Dialog öffnen um eine Zeit für test zu wählen',
      closeButton: 'Schliessen',
      timeInWrongLocale: '9:52 AM',
      validationMessage: 'Ungültiges Format, bitte gib die Zeit im Format HH:MM ein',
    },
    en: {
      time1: '9:52 AM',
      time2: '6:33 PM',
      time3: '12:52 AM',
      firstHour: '12',
      labelText: 'Open dialog to select a time for test',
      closeButton: 'Close',
      timeInWrongLocale: '09:52',
      validationMessage:
        'Invalid format, please enter the time in the format HH:MM AM/PM',
    },
  }

  describe.each(Object.entries(localeData))('in locale %s', (locale, data) => {
    beforeEach(() => {
      setTestLocale(locale)
    })

    it('renders the component', async () => {
      // given

      // when
      render(ETimePicker, {
        props: {
          value: TIME1_ISO,
          name: 'test',
        },
      })

      // then
      await screen.findByDisplayValue(data.time1)
      screen.getByLabelText(data.labelText)
    })

    it('looks like a time picker', async () => {
      // given

      // when
      const { container } = render(ETimePicker, {
        props: { value: TIME1_ISO, name: 'test' },
      })

      // then
      expect(snapshotOf(container)).toMatchSnapshot('pickerclosed')

      // when
      await user.click(screen.getByLabelText(data.labelText))

      // then
      await screen.findByText(data.closeButton)
      expect(snapshotOf(container)).toMatchSnapshot('pickeropen')
    })

    it('allows setting a different valueFormat', async () => {
      // given

      // when
      render(ETimePicker, {
        props: {
          value: TIME1_HHMM,
          name: 'test',
          valueFormat: 'HH:mm',
        },
      })

      // then
      await screen.findByDisplayValue(data.time1)
      screen.getByLabelText(data.labelText)
    })

    it('opens the picker when the text field is clicked', async () => {
      // given
      render(ETimePicker, {
        props: { value: TIME1_ISO, name: 'test' },
      })
      const inputField = await screen.findByDisplayValue(data.time1)

      // when
      await user.click(inputField)

      // then
      await waitFor(async () => {
        expect(await screen.findByText(data.closeButton)).toBeVisible()
      })
    })

    it('closes the picker when clicking the close button', async () => {
      // given
      render(ETimePicker, {
        props: { value: TIME1_ISO, name: 'test' },
      })
      const inputField = await screen.findByDisplayValue(data.time1)
      user.click(inputField)
      await waitFor(async () => {
        expect(await screen.findByText(data.closeButton)).toBeVisible()
      })
      const closeButton = screen.getByText(data.closeButton)

      // when
      await user.click(closeButton)

      // then
      await waitFor(async () => {
        expect(await screen.queryByText(data.closeButton)).not.toBeVisible()
      })
    })

    it('closes the picker when clicking outside', async () => {
      // given
      render(ETimePicker, {
        props: { value: TIME1_ISO, name: 'test' },
      })
      const inputField = await screen.findByDisplayValue(data.time1)
      user.click(inputField)
      await waitFor(async () => {
        expect(await screen.findByText(data.closeButton)).toBeVisible()
      })

      // when
      await user.click(document.body)

      // then
      await waitFor(async () => {
        expect(await screen.queryByText(data.closeButton)).not.toBeVisible()
      })
    })

    it('closes the picker when pressing escape', async () => {
      // given
      render(ETimePicker, {
        props: { value: TIME1_ISO, name: 'test' },
      })
      const inputField = await screen.findByDisplayValue(data.time1)
      user.click(inputField)
      await waitFor(async () => {
        expect(await screen.findByText(data.closeButton)).toBeVisible()
      })

      // when
      await user.click(document.body)

      // then
      await waitFor(async () => {
        expect(await screen.queryByText(data.closeButton)).not.toBeVisible()
      })
    })

    it('does not close the picker when selecting a time', async () => {
      // given
      render(ETimePicker, {
        props: { value: TIME1_ISO, name: 'test' },
      })
      const inputField = await screen.findByDisplayValue(data.time1)
      user.click(inputField)
      await waitFor(async () => {
        expect(await screen.findByText(data.closeButton)).toBeVisible()
      })

      // when
      // Click the 0th hour
      await user.click(await screen.findByText(data.firstHour))
      // Click the 45th minute
      await user.click(await screen.findByText('45'))

      // then
      // close button should stay visible
      await expect(
        waitFor(() => {
          expect(screen.queryByText(data.closeButton)).not.toBeVisible()
        })
      ).rejects.toThrow(/Received element is visible/)
    })

    it('updates v-model when the input field is changed', async () => {
      // given
      const { emitted } = render(ETimePicker, {
        props: { value: TIME1_ISO, name: 'test' },
      })
      const inputField = await screen.findByDisplayValue(data.time1)

      // when
      await user.clear(inputField)
      await user.keyboard(data.time2)

      // then
      await waitFor(async () => {
        const events = emitted().input
        // some input events were fired
        expect(events.length).toBeGreaterThan(0)
        // the last one included the parsed version of our entered time
        expect(events[events.length - 1]).toEqual([TIME2_ISO])
      })
      // Our entered time should be visible...
      screen.getByDisplayValue(data.time2)
      // ...and stay visible
      await expect(
        waitFor(() => {
          expect(screen.getByDisplayValue(data.time2)).not.toBeVisible()
        })
      ).rejects.toThrow(/Received element is visible/)
    })

    it('updates v-model when a new time is selected in the picker', async () => {
      // given
      const { emitted } = render(ETimePicker, {
        props: { value: TIME1_ISO, name: 'test' },
      })
      await screen.findByDisplayValue(data.time1)

      // when
      // click the button to open the picker
      await user.click(screen.getByLabelText(data.labelText))
      // Click the 0th hour. We can only click this one, because
      // testing library is missing the vuetify styles, and all the
      // number elements overlap
      await user.click(await screen.findByText(data.firstHour))
      // click the close button
      await user.click(screen.getByText(data.closeButton))

      // then
      await waitFor(() => {
        expect(screen.queryByText(data.closeButton)).not.toBeVisible()
      })
      await waitFor(async () => {
        const events = emitted().input
        // some input events were fired
        expect(events.length).toBeGreaterThan(0)
        // the last one included the parsed version of our entered time
        expect(events[events.length - 1]).toEqual([TIME3_ISO])
      })
      // Our entered time should be visible...
      screen.getByDisplayValue(data.time3)
      // ...and stay visible
      await expect(
        waitFor(() => {
          expect(screen.getByDisplayValue(data.time3)).not.toBeVisible()
        })
      ).rejects.toThrow(/Received element is visible/)
    })

    it('validates the input', async () => {
      // given
      render(ETimePicker, {
        props: { value: TIME1_ISO, name: 'test' },
      })
      const inputField = await screen.findByDisplayValue(data.time1)

      // when
      await user.clear(inputField)
      await user.keyboard(data.timeInWrongLocale)

      // then
      await screen.findByText(data.validationMessage)
    })

    it('works with invalid initialization', async () => {
      // given

      // when
      render(ETimePicker, {
        props: {
          value: 'abc',
          name: 'test',
        },
      })

      // then
      const inputField = await screen.findByDisplayValue('Invalid Date')

      // when
      await user.clear(inputField)
      await user.keyboard(data.time2)

      // then
      waitFor(() => {
        expect(screen.queryByText('Invalid Date')).not.toBeInTheDocument()
      })
    })
  })
})
