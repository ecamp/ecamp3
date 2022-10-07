import campCollaborationDisplayName from '@/../common/helpers/campCollaborationDisplayName.js'

const dayResponsiblesCommaSeparated = (day, tc) => {
  return day
    .dayResponsibles()
    .items.map((dayResponsible) =>
      campCollaborationDisplayName(dayResponsible.campCollaboration(), tc)
    )
    .join(', ')
}

export { dayResponsiblesCommaSeparated }
