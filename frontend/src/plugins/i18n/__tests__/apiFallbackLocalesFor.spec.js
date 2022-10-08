import { fallbackLocale } from '@/plugins/i18n'
import fallbackLocalesFor from '@/plugins/i18n/apiFallbackLocalesFor'

describe('apiFallbackLocales', () => {
  ;[null, undefined, 1, [], {}].forEach((param) => {
    it(`returns fallbackLocale if input is not a string, but ${param}`, () => {
      expect([...fallbackLocalesFor(param)]).toStrictEqual([fallbackLocale])
    })

    it('returns fallbackLocale if string does not contain dashes', () => {
      expect([...fallbackLocalesFor('de')]).toStrictEqual([fallbackLocale])
    })

    Object.entries({
      de_CH_scout: ['de_CH', 'de', fallbackLocale],
      de_CH: ['de', fallbackLocale],
      fr_CH_scout: ['fr_CH', 'fr', fallbackLocale],
      fr_CH: ['fr', fallbackLocale],
      it_CH_scout: ['it_CH', 'it', fallbackLocale],
      it_CH: ['it', fallbackLocale],
      en_CH_scout: ['en_CH', 'en', fallbackLocale],
      en_CH: ['en', fallbackLocale],
    }).forEach((entry) => {
      const locale = entry[0]
      const result = entry[1]
      it(`returns correct fallbacks [${result}] for locale ${locale}`, () => {
        expect([...fallbackLocalesFor(locale)]).toStrictEqual(result)
      })
    })
  })
})
