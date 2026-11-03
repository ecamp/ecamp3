import { describe, expect, it } from "vitest";
import campCollaborationInitials from '../campCollaborationInitials.js'

describe('campCollaborationInitials', () => {
  it.each([
    [{}, ''],
    [null, ''],
    [undefined, ''],
    [{ inviteEmail: 'ecamp@ecamp3.ch', user: null }, 'EC'],
    [{ inviteEmail: null, user: () => ({ displayName: 'Bi-Pi' }) }, 'BP'],
    [{ inviteEmail: null, _meta: {} }, ''],
    [{ inviteEmail: null, _meta: { loading: true } }, ''],
    [{ inviteEmail: null, user: () => ({ _meta: { loading: true } }) }, ''],
    [
      { inviteEmail: null, status: 'inactive', user: () => ({ displayName: 'Bam' }) },
      'BA',
    ],
    [{ inviteEmail: 'ecamp@ecamp3.ch', status: 'inactive', user: null }, 'EC'],
    [{ abbreviation: 'B', inviteEmail: null, user: null }, 'B'],
    [{ abbreviation: 'AA', user: () => ({ displayName: 'Bi-Pi' }) }, 'AA'],
    [
      { abbreviation: 'AA', user: () => ({ abbreviation: 'CC', displayName: 'Bi-Pi' }) },
      'AA',
    ],
    [{ user: () => ({ abbreviation: 'QQ' }) }, 'QQ'],
    [{ user: () => ({ abbreviation: 'QQ', displayName: 'Bi-Pi' }) }, 'QQ'],
  ])('maps %o to "%s"', (input, expected) => {
    expect(campCollaborationInitials(input)).toEqual(expected)
  })
})
