import dayjs from '@/common/helpers/dayjs.js'

// Hitobito allows longer values than eCamp, therefore values are truncated to their maximum eCamp lengths
const TITLE_MAX_LENGTH = 32
const MOTTO_MAX_LENGTH = 128
const ADDRESS_NAME_MAX_LENGTH = 128
const PERIOD_DESCRIPTION_MAX_LENGTH = 32

/**
 * Hitobito strings may contain newlines or other duplicate whitespace, collapse into one single space
 */
function collapseWhitespace(value) {
  return value.replace(/[\p{C}\s]+/gu, ' ')
}

function truncate(value, maxLength) {
  if (value === null || value === undefined) return null
  const normalized = collapseWhitespace(String(value)).trim()
  const trimmed = normalized.slice(0, maxLength).trim()
  if (trimmed === '') return null

  return trimmed
}

/**
 * Hitobito sends timestamps ('2026-02-01T00:00:00+00:00'), eCamp periods are date-only
 */
function toDate(timestamp) {
  return dayjs.utc(timestamp).format('YYYY-MM-DD')
}

/**
 * Maps a Hitobito event to an eCamp camp
 */
export function eventToCamp(event, provider) {
  const title = truncate(event.name, TITLE_MAX_LENGTH)

  return {
    title,
    motto: truncate(event.motto, MOTTO_MAX_LENGTH),
    addressName: truncate(event.location, ADDRESS_NAME_MAX_LENGTH),
    periods: (event.dates ?? []).map((date) => ({
      // optional at Hitobito, use title as fallback if not set
      description: truncate(date.label, PERIOD_DESCRIPTION_MAX_LENGTH) ?? title,
      start: toDate(date.startAt),
      // optional at Hitobito, use start as fallback if not set
      end: toDate(date.finishAt ?? date.startAt),
      hitobitoId: String(date.id),
    })),
    hitobitoProvider: provider,
    hitobitoEventId: String(event.id),
  }
}
