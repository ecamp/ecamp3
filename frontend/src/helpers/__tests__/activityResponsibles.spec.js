import { describe, expect, it } from 'vitest'
import {
  activityResponsibleNames,
  activityResponsiblesCommaSeparated,
} from '../activityResponsibles.js'

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

  it('returns empty array for null object', () => {
    expect(activityResponsibleNames(null, null)).toEqual([])
  })
})
