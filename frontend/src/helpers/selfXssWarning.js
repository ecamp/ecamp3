import i18n from '@/plugins/i18n'

/**
 * Warns about self-XSS in the browser console, the way Facebook and others do.
 */
export function warnAboutSelfXss() {
  console.log(
    `%c${i18n.global.t('global.selfXssWarning.title')}`,
    'color: #b71c1c; font-size: 48px; font-weight: bold;'
  )
  console.log(`%c${i18n.global.t('global.selfXssWarning.message')}`, 'font-size: 16px;')
}
