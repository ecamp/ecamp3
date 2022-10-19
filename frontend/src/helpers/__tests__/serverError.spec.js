import { transformViolations } from '@/helpers/serverError'
import cloneDeep from 'lodash/cloneDeep'
import { fallbackLocale } from '@/plugins/i18n'

describe('transformViolations', () => {
  describe('without i18n', () => {
    it('returns server errors as 0 property of object', () => {
      expect(transformViolations(unauthorizedError)).toEqual({ 0: 'Unauthorized' })
    })

    it('returns a map of propertyPath => violations[]', () => {
      const validationError = createValidationError()
        .withViolation({
          message: 'first message',
          propertyPath: 'parent.property',
        })
        .withViolation({
          message: 'second message',
          propertyPath: 'parent.property',
        })
        .withViolation({
          message: 'third message',
          propertyPath: 'property2',
        })
        .build()
      expect(transformViolations(validationError)).toEqual({
        'parent.property': ['first message', 'second message'],
        property2: ['third message'],
      })
    })
  })

  describe('with i18n', () => {
    const te = jest.fn()
    const tc = jest.fn()
    const i18n = { te, tc }

    beforeEach(() => {
      delete i18n.locale
      te.mockReset()
      tc.mockReset()
    })

    describe('still uses the message property of violations', () => {
      it('if the locale is null and translation does not exist', () => {
        const validationError = createValidationError()
          .withViolation({
            message: 'first message',
            propertyPath: 'property',
            i18n: {
              key: 'not.exists',
            },
          })
          .build()
        expect(transformViolations(validationError, i18n)).toEqual({
          property: ['first message'],
        })
      })

      it('if the translation for the locale is not in the response and translation does not exist', () => {
        i18n.locale = 'it'
        const validationError = createValidationError()
          .withViolation({
            message: 'first message',
            propertyPath: 'property',
            i18n: {
              key: 'not.exists',
              translations: {
                fr: 'will not be used',
              },
            },
          })
          .build()
        expect(transformViolations(validationError, i18n)).toEqual({
          property: ['first message'],
        })
      })
    })

    describe('uses the frontend translation', () => {
      it('if the key is available in the translations', () => {
        const validationError = createValidationError()
          .withViolation({
            message: 'first message',
            propertyPath: 'property',
            i18n: {
              key: 'key.exists',
              translations: {
                fr: 'will not be used',
              },
            },
          })
          .build()
        te.mockReturnValue(true)
        const translation = 'a translation'
        tc.mockReturnValue(translation)

        expect(transformViolations(validationError, i18n)).toEqual({
          property: [translation],
        })
      })

      it('even if there are translations in the response', () => {
        const validationError = createValidationError()
          .withViolation({
            message: 'first message',
            propertyPath: 'property',
            i18n: {
              key: 'key.exists',
              translations: {
                en: 'will not be used',
              },
            },
          })
          .build()
        te.mockReturnValue(true)
        const translation = 'a translation'
        tc.mockReturnValue(translation)
        i18n.locale = 'en'

        expect(transformViolations(validationError, i18n)).toEqual({
          property: [translation],
        })
      })
    })

    describe('uses the translations in the response', () => {
      it(' if the keys are not known in the frontend', () => {
        const validationError = createValidationError()
          .withViolation({
            message: 'first message',
            propertyPath: 'property',
            i18n: {
              key: 'not.exists',
              translations: {
                fr_CH_scout: 'fr_CH_scout',
              },
            },
          })
          .build()
        te.mockReturnValue(false)
        i18n.locale = 'fr-CH-scout'

        expect(transformViolations(validationError, i18n)).toEqual({
          property: ['fr_CH_scout'],
        })
      })

      it('uses the direct fallback', () => {
        const validationError = createValidationError()
          .withViolation({
            message: 'first message',
            propertyPath: 'property',
            i18n: {
              translations: {
                de_CH: 'de_CH',
              },
            },
          })
          .build()
        te.mockReturnValue(false)
        i18n.locale = 'de-CH-scout'

        expect(transformViolations(validationError, i18n)).toEqual({
          property: ['de_CH'],
        })
      })

      it('uses fallbackLocale', () => {
        const validationError = createValidationError()
          .withViolation({
            message: 'first message',
            propertyPath: 'property',
            i18n: {
              translations: {
                [fallbackLocale]: fallbackLocale,
                de: 'de',
              },
            },
          })
          .build()
        te.mockReturnValue(false)
        i18n.locale = 'it'

        expect(transformViolations(validationError, i18n)).toEqual({
          property: [fallbackLocale],
        })
      })
    })
  })
})

const unauthorizedError = {
  name: 'ServerException',
  response: {
    status: 401,
    data: {
      message: 'Unauthorized',
      detail: 'Unauthorized',
    },
  },
}

function createValidationError() {
  const validationError = {
    name: 'ServerException',
    response: {
      status: 422,
      data: {
        violations: [],
      },
    },
  }

  return {
    withViolation: function (violation) {
      validationError.response.data.violations.push(violation)
      return this
    },
    build: () => cloneDeep(validationError),
  }
}
