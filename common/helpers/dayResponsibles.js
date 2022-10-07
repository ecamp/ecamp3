import campCollaborationDisplayName from './campCollaborationDisplayName.js'

const dayResponsiblesCommaSeparated = (day, tc) => {
  if (!day) return ''

  return day
    .dayResponsibles()
    .items.map((dayResponsible) =>
      campCollaborationDisplayName(dayResponsible.campCollaboration(), tc)
    )
    .join(', ')
}

export { dayResponsiblesCommaSeparated }
