import ApiColorField from '../ApiColorField.vue'
import { fireEvent, screen, waitFor } from '@testing-library/vue'
import { render } from '@/test/renderWithVuetify.js'
import user from '@testing-library/user-event'
import { ApiMock } from '@/components/form/api/__tests__/ApiMock'
import { extend } from 'vee-validate'
import { regex } from 'vee-validate/dist/rules'

extend('regex', regex)

describe('An ApiColorField', () => {
  let apiMock

  const FIELD_PATH = 'test-field/123'
  const FIELD_LABEL = 'Test field'
  const COLOR_1 = '#FF0000'
  const COLOR_2 = '#FAFFAF'

  beforeEach(() => {
    apiMock = ApiMock.create()
  })

  afterEach(() => {
    jest.restoreAllMocks()
  })

  test('triggers api.patch and status update if input changes', async () => {
    // given
    apiMock.get().thenReturn(ApiMock.success(COLOR_1).forPath(FIELD_PATH))
    apiMock.patch().thenReturn(ApiMock.success(COLOR_2))
    render(ApiColorField, {
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
    const inputField = await screen.findByLabelText(FIELD_LABEL)
    inputField.value = COLOR_2
    await fireEvent.input(inputField)
    // click the button to open the picker
    // click the save button
    await waitFor(async () => {
      await user.click(screen.getByLabelText('Speichern'))
    })

    // then
    await waitFor(async () => {
      const inputField = await screen.findByLabelText(FIELD_LABEL)
      expect(inputField.value).toBe(COLOR_2)
      expect(apiMock.getMocks().patch).toBeCalledTimes(1)
    })
  })

  test('updates state if value in store is refreshed and has new value', async () => {
    // given
    apiMock.get().thenReturn(ApiMock.networkError().forPath(FIELD_PATH))
    render(ApiColorField, {
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
    expect((await screen.findByLabelText(FIELD_LABEL)).value).not.toBe(COLOR_1)
    const retryButton = await screen.findByText('Erneut versuchen')
    apiMock.get().thenReturn(ApiMock.success(COLOR_1).forPath(FIELD_PATH))

    // when
    await user.click(retryButton)

    // then
    await waitFor(async () => {
      expect((await screen.findByLabelText(FIELD_LABEL)).value).toBe(COLOR_1)
    })
  })
})
