import { activityResponsiblesCommaSeparated } from '../activityResponsibles.js'

describe('activityResponsibles', () => {
  it('resolves camp collaboration with and without user', () => {
    expect(
      activityResponsiblesCommaSeparated(
        {
          activityResponsibles: () => ({
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
      activityResponsiblesCommaSeparated(
        {
          activityResponsibles: () => ({
            items: [],
          }),
        },
        null
      )
    ).toEqual('')
  })

  it('return empty string for null object', () => {
    expect(activityResponsiblesCommaSeparated(null, null)).toEqual('')
  })
})
