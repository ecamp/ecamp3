import dayjs from '@/common/helpers/dayjs.js'

export const JS_COACH_DAY_STATUS = {
  GREEN: 'green',
  YELLOW: 'yellow',
  RED: 'red',
}

export const DEFAULT_JS_COMPLIANCE_DAYTIMES = {
  morning: { start: '06:00', end: '12:00' },
  afternoon: { start: '12:00', end: '18:00' },
  evening: { start: '19:00', end: '23:00' },
}

export const DEFAULT_JS_COMPLIANCE_CATEGORY_PREFIXES = {
  ls: 'LS',
  la: 'LA',
}

const MIN_ACTIVITY_MINUTES = 30
const MIN_DAILY_MINUTES = 4 * 60
const MAX_LA_DAILY_MINUTES = 2 * 60

const DAYTIME_NAMES = ['morning', 'afternoon', 'evening']

function overlapMinutes(startA, endA, startB, endB) {
  const overlapStart = startA.isAfter(startB) ? startA : startB
  const overlapEnd = endA.isBefore(endB) ? endA : endB
  const minutes = overlapEnd.diff(overlapStart, 'minute')
  return Math.max(minutes, 0)
}

function categoryShort(scheduleEntry) {
  const short = scheduleEntry.activity?.()?.category?.()?.short
  return typeof short === 'string' ? short.toUpperCase() : ''
}

function dayBoundaries(date) {
  const start = dayjs.utc(date).startOf('day')
  return {
    start,
    end: start.add(1, 'day'),
  }
}

function parseTimeToMinutes(value) {
  if (typeof value !== 'string') return null
  const match = value.match(/^(\d{2}):(\d{2})$/)
  if (!match) return null

  const hours = Number(match[1])
  const minutes = Number(match[2])
  if (hours < 0 || hours > 23 || minutes < 0 || minutes > 59) return null

  return hours * 60 + minutes
}

function resolveDaytimeWindows(daytimeConfig) {
  return DAYTIME_NAMES.map((name) => {
    const fallback = DEFAULT_JS_COMPLIANCE_DAYTIMES[name]
    const configured = daytimeConfig?.[name] ?? {}
    const startMinutes =
      parseTimeToMinutes(configured.start) ?? parseTimeToMinutes(fallback.start)
    const endMinutes =
      parseTimeToMinutes(configured.end) ?? parseTimeToMinutes(fallback.end)

    return {
      name,
      startMinutes,
      endMinutes,
    }
  }).filter(
    ({ startMinutes, endMinutes }) => startMinutes !== null && endMinutes !== null
  )
}

function parseCategoryPrefixes(prefixString) {
  if (typeof prefixString !== 'string') return []
  return prefixString
    .split(';')
    .map((p) => p.trim())
    .filter((p) => p.length > 0 && p.length <= 8)
    .map((p) => p.toUpperCase())
}

function resolveCategoryPrefixes(prefixConfig, key) {
  const fallback = DEFAULT_JS_COMPLIANCE_CATEGORY_PREFIXES[key]
  const configured = prefixConfig?.[key] ?? ''
  const prefixString = configured || fallback
  return parseCategoryPrefixes(prefixString)
}

function categoryMatchesPrefixes(categoryShort, prefixes) {
  return prefixes.includes(categoryShort)
}

function isJsCategory(
  scheduleEntry,
  categoryPrefixes = DEFAULT_JS_COMPLIANCE_CATEGORY_PREFIXES
) {
  return (
    isLsCategory(scheduleEntry, categoryPrefixes) ||
    isLaCategory(scheduleEntry, categoryPrefixes)
  )
}

function isLsCategory(
  scheduleEntry,
  categoryPrefixes = DEFAULT_JS_COMPLIANCE_CATEGORY_PREFIXES
) {
  const short = categoryShort(scheduleEntry)
  const lsPrefixes = resolveCategoryPrefixes(categoryPrefixes, 'ls')
  return categoryMatchesPrefixes(short, lsPrefixes)
}

function isLaCategory(
  scheduleEntry,
  categoryPrefixes = DEFAULT_JS_COMPLIANCE_CATEGORY_PREFIXES
) {
  const short = categoryShort(scheduleEntry)
  const laPrefixes = resolveCategoryPrefixes(categoryPrefixes, 'la')
  return categoryMatchesPrefixes(short, laPrefixes)
}

export function getJsCoachDayStatus(
  date,
  scheduleEntries,
  daytimeConfig = DEFAULT_JS_COMPLIANCE_DAYTIMES,
  categoryPrefixes = DEFAULT_JS_COMPLIANCE_CATEGORY_PREFIXES
) {
  const { start: dayStart, end: dayEnd } = dayBoundaries(date)
  const daytimeWindows = resolveDaytimeWindows(daytimeConfig)

  let totalQualifiedMinutes = 0
  let laQualifiedMinutes = 0
  let qualifiedActivities = 0
  const daytimes = new Set()

  scheduleEntries.forEach((entry) => {
    if (!isJsCategory(entry, categoryPrefixes)) return

    const entryStart = dayjs.utc(entry.start)
    const entryEnd = dayjs.utc(entry.end)
    const overlapWithDay = overlapMinutes(entryStart, entryEnd, dayStart, dayEnd)

    if (overlapWithDay < MIN_ACTIVITY_MINUTES) return

    qualifiedActivities += 1
    totalQualifiedMinutes += overlapWithDay
    if (isLaCategory(entry, categoryPrefixes)) {
      laQualifiedMinutes += overlapWithDay
    }

    daytimeWindows.forEach(({ name, startMinutes, endMinutes }) => {
      const windowStart = dayStart.add(startMinutes, 'minute')
      const windowEnd = dayStart.add(endMinutes, 'minute')
      if (overlapMinutes(entryStart, entryEnd, windowStart, windowEnd) > 0) {
        daytimes.add(name)
      }
    })
  })

  const hasRequiredTime =
    totalQualifiedMinutes - Math.max(laQualifiedMinutes - MAX_LA_DAILY_MINUTES, 0) >=
      MIN_DAILY_MINUTES && qualifiedActivities >= 2

  if (!hasRequiredTime) {
    return JS_COACH_DAY_STATUS.RED
  }

  if (daytimes.size < 2) {
    return JS_COACH_DAY_STATUS.YELLOW
  }

  return JS_COACH_DAY_STATUS.GREEN
}
