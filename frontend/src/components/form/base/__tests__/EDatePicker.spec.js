import { screen, waitFor } from '@testing-library/vue'
import { render, setTestLocale, snapshotOf } from '@/test/renderWithVuetify.js'
import user from '@testing-library/user-event'
import EDatePicker from '../EDatePicker'

describe('An EDatePicker', () => {
  const DATE1_ISO = '2020-03-01'
  const DATE2_ISO = '2020-03-19'

  const localeData = {
    de: {
      date1: '01.03.2020',
      date2: '19.03.2020',
      dateInWrongLocale: '03/19/2020',
      labelText: 'Dialog öffnen um ein Datum für test zu wählen',
      date1Heading: 'März 2020',
      closeButton: 'Schliessen',
      validationMessage:
        'Ungültiges Format, bitte gib das Datum im Format DD.MM.YYYY ein',
    },
    en: {
      date1: '03/01/2020',
      date2: '03/19/2020',
      dateInWrongLocale: '19.03.2020',
      labelText: 'Open dialog to select a date for test',
      date1Heading: 'March 2020',
      closeButton: 'Close',
      validationMessage: 'Invalid format, please enter the date in the format MM/DD/YYYY',
    },
  }

  describe.each(Object.entries(localeData))('in locale %s', (locale, data) => {
    beforeEach(() => {
      setTestLocale(locale)
    })

    it('renders the component', async () => {
      // given

      // when
      render(EDatePicker, { props: { value: DATE1_ISO, name: 'test' } })

      // then
      await screen.findByDisplayValue(data.date1)
      screen.getByLabelText(data.labelText)
    })

    it('looks like a date picker', async () => {
      // given

      // when
      const { container } = render(EDatePicker, {
        props: { value: DATE1_ISO, name: 'test' },
      })

      // then
      expect(snapshotOf(container)).toMatchSnapshot('pickerclosed')

      // when
      await user.click(screen.getByLabelText(data.labelText))

      // then
      await screen.findByText(data.closeButton)
      await screen.findByText(data.date1Heading)
      expect(snapshotOf(container)).toMatchSnapshot('pickeropen')
    })

    it('updates v-model when the input field is changed', async () => {
      // given
      const { emitted } = render(EDatePicker, {
        props: { value: DATE1_ISO, name: 'test' },
      })
      const inputField = await screen.findByDisplayValue(data.date1)

      // when
      await user.clear(inputField)
      await user.keyboard(data.date2)

      // then
      await waitFor(async () => {
        const events = emitted().input
        // some input events were fired
        expect(events.length).toBeGreaterThan(0)
        // the last one included the parsed version of our entered date
        expect(events[events.length - 1]).toEqual([DATE2_ISO])
      })
      // Our entered date should be visible...
      screen.getByDisplayValue(data.date2)
      // ...and stay visible
      return expect(
        waitFor(() => {
          expect(screen.getByDisplayValue(data.date2)).not.toBeVisible()
        })
      ).rejects.toThrow(/Received element is visible/)
    })

    it('updates v-model when a new date is selected in the picker', async () => {
      // given
      const { emitted } = render(EDatePicker, {
        props: { value: DATE1_ISO, name: 'test' },
      })
      await screen.findByDisplayValue(data.date1)

      // when
      // click the button to open the picker
      await user.click(screen.getByLabelText(data.labelText))
      // click the 19th day of the month
      await user.click(screen.getByText('19'))

      // then
      await waitFor(async () => {
        const events = emitted().input
        // some input events were fired
        expect(events.length).toBeGreaterThan(0)
        // the last one included the parsed version of our entered date
        expect(events[events.length - 1]).toEqual([DATE2_ISO])
      })
      // Our selected date should be visible...
      screen.getByDisplayValue(data.date2)
      // ...and stay visible
      return expect(
        waitFor(() => {
          expect(screen.getByDisplayValue(data.date2)).not.toBeVisible()
        })
      ).rejects.toThrow(/Received element is visible/)
    })

    it('validates the input', async () => {
      // given
      render(EDatePicker, {
        props: { value: DATE1_ISO, name: 'test' },
      })
      const inputField = await screen.findByDisplayValue(data.date1)

      // when
      await user.clear(inputField)
      await user.keyboard(data.dateInWrongLocale)

      // then
      await screen.findByText(data.validationMessage)
    })
  })
})
