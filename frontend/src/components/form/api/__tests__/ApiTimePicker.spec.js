import ApiTimePicker from '../ApiTimePicker'
import { screen, waitFor } from '@testing-library/vue'
import { render, setTestLocale } from '@/test/renderWithVuetify.js'
import user from '@testing-library/user-event'
import { ApiMock } from '@/components/form/api/__tests__/ApiMock'

describe('An ApiTimePicker', () => {
  let apiMock

  const FIELD_NAME = 'test-field/123'
  const FIELD_LABEL = 'Test field'
  const TIME_1 = '2037-07-18T09:52:00+00:00'
  const TIME_2 = '2037-07-18T00:52:00+00:00'
  const PICKER_BUTTON_LABEL_TEXT = 'Dialog öffnen um eine Zeit für Test field zu wählen'

  beforeEach(() => {
    setTestLocale('de')
    apiMock = ApiMock.create()
  })

  afterEach(() => {
    jest.restoreAllMocks()
  })

  it('triggers api.patch and status update if input changes', async () => {
    // given
    apiMock.get().thenReturn(ApiMock.success(TIME_1).forFieldName(FIELD_NAME))
    apiMock.patch().thenReturn(ApiMock.success(TIME_2))
    render(ApiTimePicker, {
      props: {
        autoSave: false,
        fieldname: FIELD_NAME,
        uri: 'test-field/123',
        label: FIELD_LABEL,
        required: true,
      },
      mocks: {
        api: apiMock.getMocks(),
      },
    })

    // when
    // click the button to open the picker
    await user.click(screen.getByLabelText(PICKER_BUTTON_LABEL_TEXT))
    // Click the 0th hour. We can only click this one, because
    // testing library is missing the vuetify styles, and all the
    // number elements overlap
    await user.click(await screen.findByText('0'))
    // click the save button
    await user.click(screen.getByLabelText('Speichern'))

    // then
    await waitFor(async () => {
      const inputField = await screen.findByLabelText(FIELD_LABEL)
      expect(inputField.value).toBe('00:52')
      expect(apiMock.getMocks().patch).toBeCalledTimes(1)
    })
  })

  it('updates state if value in store is refreshed and has new value', async () => {
    // given
    apiMock.get().thenReturn(ApiMock.networkError().forFieldName(FIELD_NAME))
    render(ApiTimePicker, {
      props: {
        autoSave: false,
        fieldname: FIELD_NAME,
        uri: 'test-field/123',
        label: FIELD_LABEL,
        required: true,
      },
      mocks: {
        api: apiMock.getMocks(),
      },
    })
    await screen.findByText('A network error occurred.')
    expect((await screen.findByLabelText(FIELD_LABEL)).value).not.toBe('09:52')
    const retryButton = await screen.findByText('Erneut versuchen')
    apiMock.get().thenReturn(ApiMock.success(TIME_1).forFieldName(FIELD_NAME))

    // when
    await user.click(retryButton)

    // then
    await waitFor(async () => {
      expect((await screen.findByLabelText(FIELD_LABEL)).value).toBe('09:52')
    })
  })
})
