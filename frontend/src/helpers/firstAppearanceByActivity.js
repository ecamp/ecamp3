/**
 * Maps each activity to the index at which it first appears in the given list of
 * schedule entries.
 *
 * @param {Array} orderedScheduleEntries
 * @return {Map<string, number>} activity IRI to the index of its first schedule entry
 */
export function firstAppearanceByActivity(orderedScheduleEntries) {
  const firstAppearance = new Map()
  orderedScheduleEntries.forEach((scheduleEntry) => {
    const uri = scheduleEntry.activity()._meta.self
    if (!firstAppearance.has(uri)) firstAppearance.set(uri, firstAppearance.size)
  })
  return firstAppearance
}
