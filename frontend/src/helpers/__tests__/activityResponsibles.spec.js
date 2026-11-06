import { describe, expect, it } from 'vitest'
import { activityResponsibleNames } from '../activityResponsibles.js'

describe('activityResponsibles', () => {
  it('resolves camp collaboration with and without user', () => {
    expect(
      activityResponsibleNames(
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
    ).toEqual(['test@example.com', 'dummyUser'])
  })

  it('returns empty array without responsibles', () => {
    expect(
      activityResponsibleNames(
        {
          activityResponsibles: () => ({
            items: [],
          }),
        },
        null
      )
    ).toEqual([])
  })

  it('returns empty array for null object', () => {
    expect(activityResponsibleNames(null, null)).toEqual([])
  })

  it('returns names containing commas as single entries', () => {
    expect(
      activityResponsibleNames(
        {
          activityResponsibles: () => ({
            items: [
              {
                campCollaboration: () => ({
                  user: () => ({
                    displayName: 'Muster, Max',
                  }),
                }),
              },
              {
                campCollaboration: () => ({
                  inviteEmail: 'test@example.com',
                }),
              },
            ],
          }),
        },
        null
      )
    ).toEqual(['Muster, Max', 'test@example.com'])
  })

  it('returns empty array for undefined object', () => {
    expect(activityResponsibleNames(undefined, null)).toEqual([])
  })
})
