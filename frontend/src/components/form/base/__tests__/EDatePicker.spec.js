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
      dateShort: '2.4.2021',
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
      dateShort: '4/2/2021',
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

    it('opens the picker when the text field is clicked', async () => {
      // given
      render(EDatePicker, {
        props: { value: DATE1_ISO, name: 'test' },
      })
      const inputField = await screen.findByDisplayValue(data.date1)

      // when
      await user.click(inputField)

      // then
      await waitFor(async () => {
        expect(await screen.findByText(data.date1Heading)).toBeVisible()
      })
    })

    it('closes the picker when clicking the close button', async () => {
      // given
      render(EDatePicker, {
        props: { value: DATE1_ISO, name: 'test' },
      })
      const inputField = await screen.findByDisplayValue(data.date1)
      await user.click(inputField)
      await waitFor(async () => {
        expect(await screen.findByText(data.date1Heading)).toBeVisible()
      })
      const closeButton = screen.getByText(data.closeButton)

      // when
      await user.click(closeButton)

      // then
      await waitFor(async () => {
        expect(await screen.queryByText(data.date1Heading)).not.toBeVisible()
      })
    })

    it('closes the picker when clicking outside', async () => {
      // given
      render(EDatePicker, {
        props: { value: DATE1_ISO, name: 'test' },
      })
      const inputField = await screen.findByDisplayValue(data.date1)
      await user.click(inputField)
      await waitFor(async () => {
        expect(await screen.findByText(data.date1Heading)).toBeVisible()
      })

      // when
      await user.click(document.body)

      // then
      await waitFor(async () => {
        expect(await screen.queryByText(data.date1Heading)).not.toBeVisible()
      })
    })

    it('closes the picker when pressing escape', async () => {
      // given
      render(EDatePicker, {
        props: { value: DATE1_ISO, name: 'test' },
      })
      const inputField = await screen.findByDisplayValue(data.date1)
      await user.click(inputField)
      await waitFor(async () => {
        expect(await screen.findByText(data.date1Heading)).toBeVisible()
      })

      // when
      await user.keyboard('{Escape}')

      // then
      await waitFor(async () => {
        expect(await screen.queryByText(data.date1Heading)).not.toBeVisible()
      })
    })

    it('closes the picker when selecting a date', async () => {
      // given
      render(EDatePicker, {
        props: { value: DATE1_ISO, name: 'test' },
      })
      const inputField = await screen.findByDisplayValue(data.date1)
      await user.click(inputField)
      await waitFor(async () => {
        expect(await screen.findByText(data.date1Heading)).toBeVisible()
      })

      // when
      // click the 19th day of the month
      await user.click(screen.getByText('19'))

      // then
      await waitFor(async () => {
        expect(await screen.queryByText(data.date1Heading)).not.toBeVisible()
      })
    })

    it('re-opens the picker when clicking the text field again after selecting a date', async () => {
      // given
      render(EDatePicker, {
        props: { value: DATE1_ISO, name: 'test' },
      })
      const inputField = await screen.findByDisplayValue(data.date1)
      await user.click(inputField)
      await waitFor(async () => {
        expect(await screen.findByText(data.date1Heading)).toBeVisible()
      })
      // click the 19th day of the month
      await user.click(screen.getByText('19'))
      await waitFor(async () => {
        expect(await screen.queryByText(data.date1Heading)).not.toBeVisible()
      })

      // when
      await user.click(inputField)

      // then
      await waitFor(async () => {
        expect(await screen.findByText(data.date1Heading)).toBeVisible()
      })
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

    it('allows inputting a date in short format', async () => {
      // given
      render(EDatePicker, {
        props: { value: DATE1_ISO, name: 'test' },
      })
      const inputField = await screen.findByDisplayValue(data.date1)

      // when
      await user.clear(inputField)
      await user.keyboard(data.dateShort)

      // then
      expect(screen.queryByText(data.validationMessage)).not.toBeInTheDocument()
      // validation message should not appear
      return expect(screen.findByText(data.validationMessage)).rejects.toThrow(
        /Unable to find an element with the text/
      )
    })
  })
})
