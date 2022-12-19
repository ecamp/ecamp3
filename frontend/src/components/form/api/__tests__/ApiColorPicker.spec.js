import ApiColorPicker from '../ApiColorPicker'
import { screen, waitFor } from '@testing-library/vue'
import { render } from '@/test/renderWithVuetify.js'
import user from '@testing-library/user-event'
import { ApiMock } from '@/components/form/api/__tests__/ApiMock'
import { extend } from 'vee-validate'
import { regex } from 'vee-validate/dist/rules'

extend('regex', regex)

describe('An ApiColorPicker', () => {
  let apiMock

  const FIELD_NAME = 'test-field/123'
  const FIELD_LABEL = 'Test field'
  const COLOR_1 = '#FF0000'
  const COLOR_2 = '#FAFFAF'
  const PICKER_BUTTON_LABEL_TEXT = 'Dialog öffnen um eine Farbe für Test field zu wählen'

  beforeEach(() => {
    apiMock = ApiMock.create()
  })

  afterEach(() => {
    jest.restoreAllMocks()
  })

  test('triggers api.patch and status update if input changes', async () => {
    // given
    apiMock.get().thenReturn(ApiMock.success(COLOR_1).forFieldName(FIELD_NAME))
    apiMock.patch().thenReturn(ApiMock.success(COLOR_2))
    const { container } = render(ApiColorPicker, {
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
    // click inside the color picker canvas to select a different color
    const canvas = container.querySelector('canvas')
    await user.click(canvas, { clientX: 10, clientY: 10 })
    // click the save button
    await user.click(screen.getByLabelText('Speichern'))

    // then
    await waitFor(async () => {
      const inputField = await screen.findByLabelText(FIELD_LABEL)
      expect(inputField.value).toBe(COLOR_2)
      expect(apiMock.getMocks().patch).toBeCalledTimes(1)
    })
  })

  test('updates state if value in store is refreshed and has new value', async () => {
    // given
    apiMock.get().thenReturn(ApiMock.networkError().forFieldName(FIELD_NAME))
    render(ApiColorPicker, {
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
    expect((await screen.findByLabelText(FIELD_LABEL)).value).not.toBe(COLOR_1)
    const retryButton = await screen.findByText('Erneut versuchen')
    apiMock.get().thenReturn(ApiMock.success(COLOR_1).forFieldName(FIELD_NAME))

    // when
    await user.click(retryButton)

    // then
    await waitFor(async () => {
      expect((await screen.findByLabelText(FIELD_LABEL)).value).toBe(COLOR_1)
    })
  })
})
