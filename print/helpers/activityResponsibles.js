import campCollaborationDisplayName from '@/../common/helpers/campCollaborationDisplayName.js'

/**
 * Returns a display name for a camp collaboration based on its status
 */
const responsiblesCommaSeparated = (activity, tc) => {
  return activity
    .activityResponsibles()
    .items.map((activityResponsible) =>
      campCollaborationDisplayName(activityResponsible.campCollaboration(), tc)
    )
    .join(', ')
}

export { responsiblesCommaSeparated }
