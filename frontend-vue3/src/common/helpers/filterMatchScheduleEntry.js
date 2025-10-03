/**
 * Filtering of schedule entries by various criteria.
 * Only the matcher function for matching a single scheduleEntry with
 * a set of filter criteria is implemented here, because this can be
 * used more flexibly in the application.
 */
const filterMatchScheduleEntry = (scheduleEntry, filter) => {
  if (!filter) return true
  return (
    // filter by period
    (filter.period === null ||
      filter.period === undefined ||
      scheduleEntry.period()._meta.self === filter.period) &&
    // filter by days: OR filter
    (filter.day === null ||
      filter.day === undefined ||
      filter.day.length === 0 ||
      filter.day.includes(
        scheduleEntry.day()._meta.self
      )) &&
    // filter by categories: OR filter
    (filter.category === null ||
      filter.category === undefined ||
      filter.category.length === 0 ||
      filter.category.includes(
        scheduleEntry.activity().category()._meta.self
      )) &&
    // filter by responsibles: AND filter
    (filter.responsible === null ||
      filter.responsible === undefined ||
      filter.responsible.length === 0 ||
      filter.responsible.every((responsible) => {
        return scheduleEntry
          .activity()
          .activityResponsibles()
          .items.map((responsible) => responsible.campCollaboration()._meta.self)
          .includes(responsible)
      }) ||
      (filter.responsible[0] === 'none' &&
        scheduleEntry.activity().activityResponsibles().items.length === 0)) &&
    (filter.progressLabel === null ||
      filter.progressLabel === undefined ||
      filter.progressLabel.length === 0 ||
      filter.progressLabel.includes(
        scheduleEntry.activity().progressLabel?.()._meta.self ?? 'none'
      ))
  )
}

export { filterMatchScheduleEntry }
