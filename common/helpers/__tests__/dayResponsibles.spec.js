import { dayResponsiblesCommaSeparated } from '../dayResponsibles'

describe('dayResponsibles', () => {
  it('resolves camp collaboration with and without user', () => {
    expect(
      dayResponsiblesCommaSeparated(
        {
          dayResponsibles: () => ({
            items: [
              {
                campCollaboration: () => ({
                  inviteEmail: 'test@example.com',
                }),
              },
              {
                campCollaboration: () => ({
                  user: () => ({
                    displayName: 'dummyUser',
                  }),
                }),
              },
            ],
          }),
        },
        null
      )
    ).toEqual('test@example.com, dummyUser')
  })

  it('return empty string if no resonsibles', () => {
    expect(
      dayResponsiblesCommaSeparated(
        {
          dayResponsibles: () => ({
            items: [],
          }),
        },
        null
      )
    ).toEqual('')
  })

  it('return empty string for null object', () => {
    expect(dayResponsiblesCommaSeparated(null, null)).toEqual('')
  })
})
