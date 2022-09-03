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
  ])('maps %p to %p', (input, expected) => {
    expect(campCollaborationInitials(input)).toEqual(expected)
  })
})
