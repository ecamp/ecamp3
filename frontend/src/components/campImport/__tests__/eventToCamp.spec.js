import { describe, it, expect } from 'vitest'
import { eventToCamp } from '../eventToCamp.js'

const provider = 'pbsmidata'

function event(overrides = {}) {
  return {
    id: 123,
    name: 'Testlager',
    motto: 'Testmotto',
    location: 'Testort',
    dates: [
      {
        id: 789,
        label: 'Hauptlager',
        startAt: '2026-01-01T00:00:00+00:00',
        finishAt: '2026-02-01T00:00:00+00:00',
      },
    ],
    ...overrides,
  }
}

describe('eventToCamp', () => {
  it('maps an event onto a camp payload', () => {
    expect(eventToCamp(event(), provider)).toEqual({
      title: 'Testlager',
      motto: 'Testmotto',
      addressName: 'Testort',
      periods: [
        {
          description: 'Hauptlager',
          start: '2026-01-01',
          end: '2026-02-01',
          hitobitoId: '789',
        },
      ],
      hitobitoProvider: 'pbsmidata',
      hitobitoEventId: '123',
    })
  })

  it('sends the event id as a string', () => {
    expect(eventToCamp(event({ id: 7 }), provider).hitobitoEventId).toBe('7')
  })

  describe('truncation', () => {
    it('truncates the title to 32 characters', () => {
      const camp = eventToCamp(event({ name: 'a'.repeat(40) }), provider)
      expect(camp.title).toHaveLength(32)
    })

    it('keeps a title of exactly 32 characters', () => {
      const camp = eventToCamp(event({ name: 'a'.repeat(32) }), provider)
      expect(camp.title).toHaveLength(32)
    })

    it('truncates the motto to 128 characters', () => {
      const camp = eventToCamp(event({ motto: 'a'.repeat(200) }), provider)
      expect(camp.motto).toHaveLength(128)
    })

    it('truncates the location to 128 characters', () => {
      const camp = eventToCamp(event({ location: 'a'.repeat(70000) }), provider)
      expect(camp.addressName).toHaveLength(128)
    })

    it('truncates the period description to 32 characters', () => {
      const camp = eventToCamp(
        event({
          dates: [{ label: 'a'.repeat(40), startAt: '2026-01-01T00:00:00+00:00' }],
        }),
        provider
      )
      expect(camp.periods[0].description).toHaveLength(32)
    })
  })

  describe('whitespace', () => {
    it('collapses line breaks in the location into spaces', () => {
      const camp = eventToCamp(
        event({
          location: 'Wiese oberhalb vom Pfadiheim Richtung Süden\n\nNeukirch-Egnach',
        }),
        provider
      )

      expect(camp.addressName).toBe(
        'Wiese oberhalb vom Pfadiheim Richtung Süden Neukirch-Egnach'
      )
    })
  })

  describe('missing values', () => {
    it('normalizes an empty motto and location to null', () => {
      const camp = eventToCamp(event({ motto: '', location: null }), provider)
      expect(camp.motto).toBeNull()
      expect(camp.addressName).toBeNull()
    })

    it('falls back to the camp title when the period has no label', () => {
      const camp = eventToCamp(
        event({ dates: [{ label: null, startAt: '2026-01-01T00:00:00+00:00' }] }),
        provider
      )
      expect(camp.periods[0].description).toBe('Testlager')
    })

    it('falls back to the camp title when the period label is empty', () => {
      const camp = eventToCamp(
        event({ dates: [{ label: '  ', startAt: '2026-01-01T00:00:00+00:00' }] }),
        provider
      )
      expect(camp.periods[0].description).toBe('Testlager')
    })

    it('uses a truncated title as the period description fallback', () => {
      const camp = eventToCamp(
        event({
          name: 'a'.repeat(40),
          dates: [{ label: null, startAt: '2026-01-01T00:00:00+00:00' }],
        }),
        provider
      )
      expect(camp.periods[0].description).toHaveLength(32)
    })

    it('ends the period on its start date when finishAt is missing', () => {
      const camp = eventToCamp(
        event({ dates: [{ label: 'x', startAt: '2026-01-01T00:00:00+00:00' }] }),
        provider
      )
      expect(camp.periods[0]).toMatchObject({ start: '2026-01-01', end: '2026-01-01' })
    })

    it('returns no periods when the event has no dates', () => {
      expect(eventToCamp(event({ dates: [] }), provider).periods).toEqual([])
      expect(eventToCamp(event({ dates: undefined }), provider).periods).toEqual([])
    })
  })

  describe('dates', () => {
    it('creates one period per date', () => {
      const camp = eventToCamp(
        event({
          dates: [
            {
              id: 1,
              label: 'Vorlager',
              startAt: '2026-01-01T00:00:00+00:00',
              finishAt: null,
            },
            {
              id: 2,
              label: 'Hauptlager',
              startAt: '2026-01-02T00:00:00+00:00',
              finishAt: '2026-01-09T00:00:00+00:00',
            },
          ],
        }),
        provider
      )
      expect(camp.periods).toEqual([
        {
          description: 'Vorlager',
          start: '2026-01-01',
          end: '2026-01-01',
          hitobitoId: '1',
        },
        {
          description: 'Hauptlager',
          start: '2026-01-02',
          end: '2026-01-09',
          hitobitoId: '2',
        },
      ])
    })

    it('parses timestamps as UTC, so midnight does not shift to the previous day', () => {
      const camp = eventToCamp(
        event({ dates: [{ label: 'x', startAt: '2026-01-01T00:00:00+00:00' }] }),
        provider
      )
      expect(camp.periods[0].start).toBe('2026-01-01')
    })

    it('keeps the UTC day for a timestamp with an offset', () => {
      const camp = eventToCamp(
        event({ dates: [{ label: 'x', startAt: '2026-01-01T23:00:00+00:00' }] }),
        provider
      )
      expect(camp.periods[0].start).toBe('2026-01-01')
    })
  })
})
