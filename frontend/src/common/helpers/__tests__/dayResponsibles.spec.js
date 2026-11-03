import { describe, expect, it } from "vitest";
import {
  dayResponsiblesCommaSeparated,
  filterDayResponsiblesByDay,
} from '../dayResponsibles'

const dayWith2Responsibles = {
  _meta: {
    self: '/day/1',
  },
  period: () => ({
    dayResponsibles: () => ({
      items: [
        {
          campCollaboration: () => ({
            inviteEmail: 'test@example.com',
          }),
          day: () => ({
            _meta: { self: '/day/1' },
          }),
        },
        {
          campCollaboration: () => ({
            user: () => ({
              displayName: 'dummyUser',
            }),
          }),
          day: () => ({
            _meta: { self: '/day/1' },
          }),
        },
        {
          campCollaboration: () => ({
            user: () => ({
              displayName: 'responsibleUserOnAnotherDay',
            }),
          }),
          day: () => ({
            _meta: { self: '/day/2' },
          }),
        },
      ],
    }),
  }),
}

const dayWithoutResponsibles = {
  period: () => ({
    dayResponsibles: () => ({
      items: [],
    }),
  }),
}

describe('dayResponsiblesCommaSeparated', () => {
  it('resolves camp collaboration with and without user', () => {
    expect(dayResponsiblesCommaSeparated(dayWith2Responsibles, null)).toEqual(
      'test@example.com, dummyUser'
    )
  })

  it('return empty string if no responsibles', () => {
    expect(dayResponsiblesCommaSeparated(dayWithoutResponsibles, null)).toEqual('')
  })

  it('return empty string for null object', () => {
    expect(dayResponsiblesCommaSeparated(null, null)).toEqual('')
  })
})

describe('filterDayResponsiblesByDay', () => {
  it('resolves camp collaboration with and without user', () => {
    expect(filterDayResponsiblesByDay(dayWith2Responsibles).length).toEqual(2)
  })

  it('return empty string if no responsibles', () => {
    expect(filterDayResponsiblesByDay(dayWithoutResponsibles)).toEqual([])
  })

  it('return empty string for null object', () => {
    expect(filterDayResponsiblesByDay(null)).toEqual([])
  })
})
