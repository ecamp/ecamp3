import { describe, it, expect, beforeEach, afterEach, vi } from 'vitest'
import {
  HITOBITO_PROVIDERS,
  clearAuthorizationAttempt,
  hasAttemptedAuthorization,
  hitobitoEventCampUri,
  hitobitoEventParticipantsUri,
  hitobitoEventWebUrl,
  hitobitoEventsUri,
  isAccessTokenInvalidError,
  isValidProvider,
  markAuthorizationAttempt,
  providerIcon,
  providerIconColor,
  redirectToHitobitoAuthorization,
} from '../hitobito.js'
import { cloneDeep } from 'lodash-es'
import { getEnv } from '@/environment.js'

describe('isValidProvider', () => {
  it.each(HITOBITO_PROVIDERS)('accepts the supported provider %s', (provider) => {
    expect(isValidProvider(provider)).toBe(true)
  })

  it.each(['cevidb', 'jubladb', 'unknownprovider', '', undefined])(
    'rejects %s',
    (provider) => {
      expect(isValidProvider(provider)).toBe(false)
    }
  )
})

describe('providerIcon', () => {
  it('returns the same icon as the OAuth login for MiData', () => {
    expect(providerIcon('pbsmidata')).toBe('$pbs')
  })

  it('returns null for an unknown provider', () => {
    expect(providerIcon('unknownprovider')).toBeNull()
  })

  it('returns the MiData brand colour, so the logo is not rendered monochrome', () => {
    expect(providerIconColor('pbsmidata')).toBe('#521d3a')
  })

  it('returns no colour for an unknown provider', () => {
    expect(providerIconColor('unknownprovider')).toBeNull()
  })
})

describe('hitobitoEventsUri', () => {
  it('builds the collection URI', () => {
    expect(hitobitoEventsUri('pbsmidata')).toBe('/hitobito/pbsmidata/events')
  })

  it('builds the item URI', () => {
    expect(hitobitoEventsUri('pbsmidata', '123')).toBe('/hitobito/pbsmidata/events/123')
  })
})

describe('hitobitoEventCampUri', () => {
  it('builds the deep link URI', () => {
    expect(hitobitoEventCampUri('pbsmidata', '123')).toBe(
      '/hitobito/pbsmidata/events/123/camp'
    )
  })
})

describe('hitobitoEventParticipantsUri', () => {
  it('builds the participants URI', () => {
    expect(hitobitoEventParticipantsUri('pbsmidata', '123')).toBe(
      '/hitobito/pbsmidata/events/123/participants'
    )
  })
})

describe('hitobitoEventWebUrl', () => {
  const envBackup = cloneDeep(getEnv())

  afterEach(() => {
    window.environment = cloneDeep(envBackup)
  })

  it('links to the event page on the configured Hitobito instance', () => {
    window.environment = { HITOBITO_PBSMIDATA_BASE_URL: 'https://db.scout.ch' }
    expect(hitobitoEventWebUrl('pbsmidata', '123')).toBe('https://db.scout.ch/events/123')
  })

  it('returns null for a provider without a configured Hitobito instance', () => {
    expect(hitobitoEventWebUrl('unknownprovider', '123')).toBeNull()
  })
})

describe('isAccessTokenInvalidError', () => {
  const tokenError = {
    response: { status: 403, data: { type: '/errors/hitobito-access-token-invalid' } },
  }

  it('detects a missing or expired access token', () => {
    expect(isAccessTokenInvalidError(tokenError)).toBe(true)
  })

  it('does not treat other 403s as an invalid token', () => {
    expect(
      isAccessTokenInvalidError({
        response: { status: 403, data: { type: '/errors/403' } },
      })
    ).toBe(false)
  })

  it('ignores other statuses and non-server errors', () => {
    expect(isAccessTokenInvalidError({ response: { status: 404, data: {} } })).toBe(false)
    expect(isAccessTokenInvalidError(new Error('network'))).toBe(false)
    expect(isAccessTokenInvalidError(undefined)).toBe(false)
  })
})

describe('authorization attempt guard', () => {
  beforeEach(() => {
    window.sessionStorage.clear()
  })

  it('remembers and clears an attempt per provider', () => {
    expect(hasAttemptedAuthorization('pbsmidata')).toBe(false)

    markAuthorizationAttempt('pbsmidata')
    expect(hasAttemptedAuthorization('pbsmidata')).toBe(true)
    expect(hasAttemptedAuthorization('cevidb')).toBe(false)

    clearAuthorizationAttempt('pbsmidata')
    expect(hasAttemptedAuthorization('pbsmidata')).toBe(false)
  })
})

describe('redirectToHitobitoAuthorization', () => {
  let replace

  beforeEach(() => {
    window.sessionStorage.clear()
    replace = vi.fn()
    Object.defineProperty(window, 'location', {
      configurable: true,
      value: { replace },
    })
  })

  afterEach(() => {
    vi.restoreAllMocks()
  })

  it('redirects to the API with the callback as a relative path', () => {
    redirectToHitobitoAuthorization('pbsmidata', '/camps/hitobito/pbsmidata/import')
    expect(replace).toHaveBeenCalledWith(
      '/api/hitobito/pbsmidata/oauth?callback=%2Fcamps%2Fhitobito%2Fpbsmidata%2Fimport'
    )
  })

  it('keeps the eventId query parameter in the callback', () => {
    redirectToHitobitoAuthorization(
      'pbsmidata',
      '/camps/hitobito/pbsmidata/import?eventId=123'
    )
    expect(replace).toHaveBeenCalledWith(
      '/api/hitobito/pbsmidata/oauth?callback=%2Fcamps%2Fhitobito%2Fpbsmidata%2Fimport%3FeventId%3D123'
    )
  })

  it('marks the attempt, so a failing authorization does not loop', () => {
    redirectToHitobitoAuthorization('pbsmidata', '/camps/hitobito/pbsmidata/import')
    expect(hasAttemptedAuthorization('pbsmidata')).toBe(true)
  })
})
