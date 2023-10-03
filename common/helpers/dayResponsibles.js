import campCollaborationDisplayName from './campCollaborationDisplayName.js'

/**
 * Local filtering of dayResponsibles by day
 * (avoids separate network request for each day)
 */
const filterDayResponsiblesByDay = (day) => {
  if (!day) return []

  return day
    .period()
    .dayResponsibles()
    .items.filter((dayResponsible) => dayResponsible.day()._meta.self === day._meta.self)
}

const dayResponsiblesCommaSeparated = (day, tc) => {
  if (!day) return ''

  return filterDayResponsiblesByDay(day)
    .map((dayResponsible) =>
      campCollaborationDisplayName(dayResponsible.campCollaboration(), tc)
    )
    .join(', ')
}

export { filterDayResponsiblesByDay, dayResponsiblesCommaSeparated }
