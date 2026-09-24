import { eventToCamp } from '@/components/campImport/eventToCamp.js'

export const SYNCED_CAMP_FIELDS = ['title', 'motto', 'addressName']

function normalize(value) {
  return value === undefined || value === '' ? null : value
}

function diffRow(field, current, updated) {
  return {
    field,
    current: normalize(current),
    updated: normalize(updated),
    changed: normalize(current) !== normalize(updated),
  }
}

export function buildCampSync(camp, event, provider) {
  const target = eventToCamp(event, provider)
  const campRows = SYNCED_CAMP_FIELDS.map((field) =>
    diffRow(field, camp[field], target[field])
  )

  return {
    campRows,
    hasChanges: campRows.some((row) => row.changed),
  }
}

export function campPatch(campRows) {
  return Object.fromEntries(
    campRows.filter((row) => row.changed).map((row) => [row.field, row.updated])
  )
}
