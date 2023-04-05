import campCollaborationLegalName from '../campCollaborationLegalName.js'

describe('campCollaborationLegalName', () => {
  it.each([
    [{}, ''],
    [null, ''],
    [undefined, ''],
    [{ inviteEmail: 'ecamp@ecamp3.ch', user: null }, ''],
    [
      { inviteEmail: null, user: () => ({ profile: () => ({ legalName: 'Bi-Pi' }) }) },
      'Bi-Pi',
    ],
    [{ inviteEmail: null, _meta: {} }, ''],
    [{ inviteEmail: null, _meta: { loading: true } }, ''],
    [{ inviteEmail: null, user: () => ({ _meta: { loading: true } }) }, ''],
    [
      {
        inviteEmail: null,
        user: () => ({ profile: () => ({ _meta: { loading: true } }) }),
      },
      '',
    ],
  ])('maps %p to %p', (input, expected) => {
    expect(campCollaborationLegalName(input)).toEqual(expected)
  })
})
