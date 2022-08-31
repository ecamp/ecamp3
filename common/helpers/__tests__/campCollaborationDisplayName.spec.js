import campCollaborationDisplayName from '../campCollaborationDisplayName.js'

describe('campCollaborationDisplayName', () => {
  it.each([
    [{}, ''],
    [null, ''],
    [undefined, ''],
    [{ inviteEmail: 'ecamp@ecamp3.ch', user: null }, 'ecamp@ecamp3.ch'],
    [{ inviteEmail: null, user: () => ({ displayName: 'Bi-Pi' }) }, 'Bi-Pi'],
    [{ inviteEmail: null, _meta: {} }, ''],
    [{ inviteEmail: null, _meta: { loading: true } }, ''],
    [{ inviteEmail: null, user: () => ({ _meta: { loading: true } }) }, ''],
    [
      { inviteEmail: null, status: 'inactive', user: () => ({ displayName: 'Bi-Pi' }) },
      'Bi-Pi (inaktiv)',
    ],
    [
      { inviteEmail: 'ecamp@ecamp3.ch', status: 'inactive', user: null },
      'ecamp@ecamp3.ch (inaktiv)',
    ],
  ])('maps %p to %p', (input, expected) => {
    expect(campCollaborationDisplayName(input, () => 'inaktiv')).toEqual(expected)
  })

  it('does not add inactive indicator', () => {
    expect(
      campCollaborationDisplayName(
        { inviteEmail: null, status: 'inactive', user: () => ({ displayName: 'Bi-Pi' }) },
        null,
        false
      )
    ).toEqual('Bi-Pi')
  })
})
