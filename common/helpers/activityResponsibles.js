import campCollaborationDisplayName from './campCollaborationDisplayName.js'

/**
 * Returns a display name for a camp collaboration based on its status
 */
const activityResponsiblesCommaSeparated = (activity, tc) => {
  if (!activity) return ''

  return activity
    .activityResponsibles()
    .items.map((activityResponsible) =>
      campCollaborationDisplayName(activityResponsible.campCollaboration(), tc)
    )
    .join(', ')
}

export { activityResponsiblesCommaSeparated }
