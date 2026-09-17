import { describe, expect, it } from 'vitest'
import { buildCampSync, campPatch } from '../campSync.js'

const provider = 'pbsmidata'

function camp(overrides = {}) {
  return {
    title: 'Sola 2026',
    motto: 'Piraten',
    addressName: 'Pfadiheim Bern',
    ...overrides,
  }
}

function event(overrides = {}) {
  return {
    id: 123,
    name: 'Sola 2026',
    motto: 'Piraten',
    location: 'Pfadiheim Bern',
    dates: [
      {
        label: 'Hauptlager',
        startAt: '2026-07-06T00:00:00+00:00',
        finishAt: '2026-07-13T00:00:00+00:00',
      },
    ],
    ...overrides,
  }
}

function rowsByField(rows) {
  return Object.fromEntries(rows.map((row) => [row.field, row]))
}

describe('buildCampSync', () => {
  it('reports no changes when the camp already matches the event', () => {
    const sync = buildCampSync(camp(), event(), provider)

    expect(sync.hasChanges).toBe(false)
    expect(sync.campRows.every((row) => !row.changed)).toBe(true)
  })

  it('marks a changed camp field and keeps the unchanged ones', () => {
    const sync = buildCampSync(camp(), event({ motto: 'Wikinger' }), provider)
    const rows = rowsByField(sync.campRows)

    expect(sync.hasChanges).toBe(true)
    expect(rows.motto).toMatchObject({
      current: 'Piraten',
      updated: 'Wikinger',
      changed: true,
    })
    expect(rows.title.changed).toBe(false)
  })

  it('maps the event name onto the camp title', () => {
    const sync = buildCampSync(camp(), event({ name: 'Sola 2027' }), provider)

    expect(rowsByField(sync.campRows).title).toMatchObject({
      current: 'Sola 2026',
      updated: 'Sola 2027',
      changed: true,
    })
  })

  it('maps the event location onto the camp address', () => {
    const sync = buildCampSync(camp(), event({ location: 'Pfadiheim Zürich' }), provider)

    expect(rowsByField(sync.campRows).addressName).toMatchObject({
      current: 'Pfadiheim Bern',
      updated: 'Pfadiheim Zürich',
      changed: true,
    })
  })

  it('treats an empty camp value and a missing event value as equal', () => {
    const sync = buildCampSync(camp({ motto: '' }), event({ motto: null }), provider)

    expect(rowsByField(sync.campRows).motto.changed).toBe(false)
  })

  it('truncates event values to the lengths eCamp allows', () => {
    const sync = buildCampSync(camp(), event({ name: 'a'.repeat(40) }), provider)

    expect(rowsByField(sync.campRows).title.updated).toHaveLength(32)
  })
})

describe('campPatch', () => {
  it('contains only the changed fields', () => {
    const sync = buildCampSync(camp(), event({ motto: 'Wikinger' }), provider)

    expect(campPatch(sync.campRows)).toEqual({ motto: 'Wikinger' })
  })

  it('is empty when nothing changed', () => {
    const sync = buildCampSync(camp(), event(), provider)

    expect(campPatch(sync.campRows)).toEqual({})
  })
})
