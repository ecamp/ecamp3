import { describe, expect, it } from 'vitest'
import { participantDisplayName, partitionParticipants } from '../participants.js'

const participant = (email, rest = {}) => ({
  firstName: 'Ellen',
  lastName: 'Bloch',
  nickname: 'Quo',
  email,
  ...rest,
})

describe('partitionParticipants', () => {
  it('lists participants without a collaboration as new', () => {
    const bloch = participant('bloch.ellen@hitobito.example.com')
    const frauen = participant('frauen_lee@hitobito.example.com')

    const { newParticipants, existingParticipants } = partitionParticipants(
      [bloch, frauen],
      ['frauen_lee@hitobito.example.com']
    )

    expect(newParticipants).toEqual([bloch])
    expect(existingParticipants).toEqual([frauen])
  })

  it('ignores casing and surrounding whitespace when matching emails', () => {
    const bloch = participant('Bloch.Ellen@hitobito.example.com')

    const { newParticipants, existingParticipants } = partitionParticipants(
      [bloch],
      [' bloch.ellen@hitobito.example.com ']
    )

    expect(newParticipants).toEqual([])
    expect(existingParticipants).toEqual([bloch])
  })

  it('invites everybody when the camp has no collaborators yet', () => {
    const bloch = participant('bloch.ellen@hitobito.example.com')

    expect(partitionParticipants([bloch], []).newParticipants).toEqual([bloch])
  })
})

describe('participantDisplayName', () => {
  it('appends the nickname to the name', () => {
    expect(participantDisplayName(participant('bloch.ellen@hitobito.example.com'))).toBe(
      'Ellen Bloch (Quo)'
    )
  })

  it('omits a missing nickname', () => {
    expect(
      participantDisplayName(
        participant('bloch.ellen@hitobito.example.com', { nickname: null })
      )
    ).toBe('Ellen Bloch')
  })

  it('falls back to the nickname when no name is known', () => {
    expect(
      participantDisplayName(
        participant('bloch.ellen@hitobito.example.com', {
          firstName: null,
          lastName: null,
        })
      )
    ).toBe('Quo')
  })

  it('falls back to the email address when nothing else is known', () => {
    expect(
      participantDisplayName({
        firstName: null,
        lastName: null,
        nickname: null,
        email: 'bloch.ellen@hitobito.example.com',
      })
    ).toBe('bloch.ellen@hitobito.example.com')
  })
})
