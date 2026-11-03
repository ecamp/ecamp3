import campCollaborationDisplayName from './campCollaborationDisplayName.js'

/**
 * Returns a display name for a camp collaboration based on its status
 */
const activityResponsibleNames = (activity, tc) => {
  if (!activity) return []

  return activity
    .activityResponsibles()
    .items.map((activityResponsible) =>
      campCollaborationDisplayName(activityResponsible.campCollaboration(), tc)
    )
}

const activityResponsiblesCommaSeparated = (activity, tc) => {
  return activityResponsibleNames(activity, tc).join(', ')
}

export { activityResponsibleNames, activityResponsiblesCommaSeparated }
