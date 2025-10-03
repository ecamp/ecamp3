import { describe, expect, it } from "vitest";
import campCollaborationLegalName from '../campCollaborationLegalName.js'

describe('campCollaborationLegalName', () => {
  it.each([
    [{}, ''],
    [null, ''],
    [undefined, ''],
    [{ inviteEmail: 'ecamp@ecamp3.ch', user: null }, ''],
    [
      {
        inviteEmail: null,
        user: () => ({ profile: () => ({ legalName: 'Bi-Pi', _meta: {} }) }),
      },
      'Bi-Pi',
    ],
    [{ inviteEmail: null, _meta: {} }, ''],
    [{ inviteEmail: null, _meta: { loading: true } }, ''],
    [
      {
        inviteEmail: null,
        user: () => ({ _meta: { loading: true }, profile: () => ({ _meta: {} }) }),
      },
      '',
    ],
    [
      {
        inviteEmail: null,
        user: () => ({ profile: () => ({ _meta: { loading: true, _meta: {} } }) }),
      },
      '',
    ],
  ])('maps %p to %p', (input, expected) => {
    expect(campCollaborationLegalName(input)).toEqual(expected)
  })
})
