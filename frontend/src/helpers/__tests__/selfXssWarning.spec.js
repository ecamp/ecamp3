import { afterEach, beforeEach, describe, expect, it, vi } from 'vitest'
import i18n from '@/plugins/i18n'
import { warnAboutSelfXss } from '@/helpers/selfXssWarning.js'

describe('warnAboutSelfXss', () => {
  let log

  beforeEach(() => {
    log = vi.spyOn(console, 'log').mockImplementation(() => {})
    i18n.global.locale.value = 'en'
  })

  afterEach(() => {
    vi.restoreAllMocks()
  })

  it('shouts a translated warning into the console', () => {
    warnAboutSelfXss()

    const [title, message] = log.mock.calls
    expect(title[0]).toContain('Stop!')
    expect(title[1]).toContain('font-weight: bold')
    expect(message[0]).toContain('browser console is meant for developers')
  })

  it('warns in the language the user selected', () => {
    i18n.global.locale.value = 'de'

    warnAboutSelfXss()

    expect(log.mock.calls[0][0]).toContain('Stopp!')
  })

  it('leaves no untranslated keys behind', () => {
    warnAboutSelfXss()

    log.mock.calls.forEach(([text]) => expect(text).not.toContain('selfXssWarning'))
  })
})
