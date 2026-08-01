import { describe, expect, it } from 'vitest'
import { campRoleMixin } from '@/mixins/campRoleMixin'

const currentUserLink = '/users/me'

function mockThis(campCollaborations) {
  return {
    $store: { getters: { getLoggedInUser: { _meta: { self: currentUserLink } } } },
    _campCollaborations: campCollaborations,
  }
}

function loadedCollaboration(role, userSelf) {
  return {
    _meta: { loading: false },
    role,
    user: () => ({ _meta: { self: userSelf } }),
  }
}

function invitedCollaboration(role) {
  return { _meta: { loading: false }, role, user: null }
}

function loadingCollaboration() {
  return {
    _meta: { loading: true },
    get user() {
      return () => {
        throw new Error('user() must not be called on a loading collaboration')
      }
    },
  }
}

describe('campRoleMixin._campRole', () => {
  it('returns the current user role and ignores invited collaborators', () => {
    const collaborations = [
      invitedCollaboration('member'),
      loadedCollaboration('member', '/users/other'),
      loadedCollaboration('manager', currentUserLink),
    ]

    expect(campRoleMixin.computed._campRole.call(mockThis(collaborations))).toBe(
      'manager'
    )
  })

  it('does not resolve user() on still-loading collaborations', () => {
    const collaborations = [
      loadingCollaboration(),
      loadedCollaboration('manager', currentUserLink),
    ]

    expect(() =>
      campRoleMixin.computed._campRole.call(mockThis(collaborations))
    ).not.toThrow()
    expect(campRoleMixin.computed._campRole.call(mockThis(collaborations))).toBe(
      'manager'
    )
  })

  it('returns null while collaborations are still loading and no match found yet', () => {
    const collaborations = [
      loadingCollaboration(),
      loadedCollaboration('member', '/users/other'),
    ]

    expect(campRoleMixin.computed._campRole.call(mockThis(collaborations))).toBeNull()
  })

  it('returns undefined when the user is not a collaborator and all are loaded', () => {
    const collaborations = [
      invitedCollaboration('member'),
      loadedCollaboration('member', '/users/other'),
    ]

    expect(
      campRoleMixin.computed._campRole.call(mockThis(collaborations))
    ).toBeUndefined()
  })
})

describe('campRoleMixin.isOutsider', () => {
  function isOutsider(campCollaborations) {
    const context = mockThis(campCollaborations)
    context._campRole = campRoleMixin.computed._campRole.call(context)
    return campRoleMixin.computed.isOutsider.call(context)
  }

  it('is true for a user without collaboration in a loaded camp', () => {
    expect(
      isOutsider([
        invitedCollaboration('member'),
        loadedCollaboration('member', '/users/other'),
      ])
    ).toBe(true)
  })

  it.each(['guest', 'member', 'manager'])('is false for a %s', (role) => {
    expect(
      isOutsider([
        loadedCollaboration('member', '/users/other'),
        loadedCollaboration(role, currentUserLink),
      ])
    ).toBe(false)
  })

  it('is false while collaborations are still loading', () => {
    expect(
      isOutsider([loadingCollaboration(), loadedCollaboration('member', '/users/other')])
    ).toBe(false)
  })

  it('is false while the camp itself has not loaded its collaborations', () => {
    expect(isOutsider([])).toBe(false)
  })
})
