import { fallbackLocale } from '@/plugins/i18n/index'

export default function* (locale, separator = '_') {
  if (typeof locale === 'string') {
    const parts = locale.split(separator)
    for (let i = parts.length - 1; i > 0; i--) {
      let slice = parts.slice(0, i)
      yield slice.join(separator)
      if (slice.join('-') in fallbackLocale) {
        for (const fallback of fallbackLocale[slice.join('-')]) {
          yield fallback.split('-').join(separator)
        }
      }
    }
    if (locale.split(separator).join('-') in fallbackLocale) {
      for (const fallback of fallbackLocale[locale.split(separator).join('-')]) {
        yield fallback.split('-').join(separator)
      }
    }
  }
  if (typeof fallbackLocale === 'string') {
    yield fallbackLocale
  } else if (Array.isArray(fallbackLocale)) {
    for (const fallback of fallbackLocale) {
      yield fallback
    }
  } else {
    if ('default' in fallbackLocale) {
      for (const fallback of fallbackLocale.default) {
        yield fallback
      }
    }
  }
}
