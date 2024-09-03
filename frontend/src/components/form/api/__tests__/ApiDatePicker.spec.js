import { describe, beforeEach, afterEach, vi, test, expect, it } from 'vitest'
import ApiDatePicker from '../ApiDatePicker.vue'
import { screen, waitFor } from '@testing-library/vue'
import { render, setTestLocale } from '@/test/renderWithVuetify.js'
import user from '@testing-library/user-event'
import { ApiMock } from '@/components/form/api/__tests__/ApiMock'

describe('An ApiDatePicker', () => {
  let apiMock

  const FIELD_PATH = 'test-field/123'
  const FIELD_LABEL = 'Test field'
  const DATE_1 = '2020-03-01'
  const DATE_2 = '2020-03-19'
  const PICKER_BUTTON_LABEL_TEXT = 'Dialog öffnen, um ein Datum für Test field zu wählen'

  beforeEach(() => {
    setTestLocale('de')
    apiMock = ApiMock.create()
  })

  afterEach(() => {
    vi.restoreAllMocks()
  })

  it('triggers api.patch and status update if input changes', async () => {
    // given
    apiMock.get().thenReturn(ApiMock.success(DATE_1).forPath(FIELD_PATH))
    apiMock.patch().thenReturn(ApiMock.success(DATE_2))
    render(ApiDatePicker, {
      props: {
        autoSave: false,
        path: FIELD_PATH,
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
    // click the 19th day of the month
    await user.click(screen.getByText('19'))
    // click the save button
    await user.click(screen.getByLabelText('Speichern'))

    // then
    await waitFor(async () => {
      const inputField = await screen.findByLabelText(FIELD_LABEL)
      expect(inputField.value).toBe('19.03.2020')
      expect(apiMock.getMocks().patch).toBeCalledTimes(1)
    })
  })

  test('updates state if value in store is refreshed and has new value', async () => {
    // given
    apiMock.get().thenReturn(ApiMock.networkError().forPath(FIELD_PATH))
    render(ApiDatePicker, {
      props: {
        autoSave: false,
        path: FIELD_PATH,
        uri: 'test-field/123',
        label: FIELD_LABEL,
        required: true,
      },
      mocks: {
        api: apiMock.getMocks(),
      },
    })
    await screen.findByText('A network error occurred.')
    expect((await screen.findByLabelText(FIELD_LABEL)).value).not.toBe('01.03.2020')
    const retryButton = await screen.findByText('Erneut versuchen')
    apiMock.get().thenReturn(ApiMock.success(DATE_1).forPath(FIELD_PATH))

    // when
    await user.click(retryButton)

    // then
    await waitFor(async () => {
      expect((await screen.findByLabelText(FIELD_LABEL)).value).toBe('01.03.2020')
    })
  })
})
