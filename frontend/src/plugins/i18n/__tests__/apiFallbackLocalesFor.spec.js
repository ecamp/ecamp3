import fallbackLocalesFor from '@/plugins/i18n/apiFallbackLocalesFor'

describe('apiFallbackLocales', () => {
  it.each([null, undefined, 1, [], {}])(
    `returns fallbackLocale if input is not a string, but %s`,
    (param) => {
      expect([...fallbackLocalesFor(param)]).toStrictEqual(['en'])
    }
  )

  it('returns fallbackLocale if string does not contain dashes', () => {
    expect([...fallbackLocalesFor('rm')]).toStrictEqual(['de', 'en'])
  })

  it.each([
    { locale: 'de_CH_scout', fallbacks: ['de_CH', 'de', 'en'] },
    { locale: 'de_CH', fallbacks: ['de', 'en'] },
    { locale: 'fr_CH_scout', fallbacks: ['fr_CH', 'fr', 'en'] },
    { locale: 'fr_CH', fallbacks: ['fr', 'en'] },
    { locale: 'it_CH_scout', fallbacks: ['it_CH', 'it', 'en'] },
    { locale: 'it_CH', fallbacks: ['it', 'en'] },
    { locale: 'en_CH_scout', fallbacks: ['en_CH', 'en', 'en'] },
    { locale: 'en_CH', fallbacks: ['en', 'en'] },
    { locale: 'rm_CH_scout', fallbacks: ['rm_CH', 'rm', 'de', 'en'] },
    { locale: 'rm_CH', fallbacks: ['rm', 'de', 'en'] },
  ])(
    'returns correct fallbacks for locale $locale: [$fallbacks]',
    ({ locale, fallbacks }) => {
      expect([...fallbackLocalesFor(locale)]).toStrictEqual(fallbacks)
    }
  )
})
