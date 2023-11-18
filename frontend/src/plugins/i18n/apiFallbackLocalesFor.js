import { fallbackLocale } from '@/plugins/i18n/index'

export default function* (locale) {
  if (typeof locale !== 'string') {
    yield fallbackLocale
    return
  }
  const parts = locale.split('_')
  for (let i = parts.length - 1; i > 0; i--) {
    yield parts.slice(0, i).join('_')
  }
  yield fallbackLocale
}
