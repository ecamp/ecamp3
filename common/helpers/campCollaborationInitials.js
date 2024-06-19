import campCollaborationDisplayName from './campCollaborationDisplayName.js'
import initials from './initials.js'

/**
 * Returns two characters to display for a camp collaboration based on its user
 */
export default function (campCollaboration) {
  return campCollaboration?.abbreviation
    ? campCollaboration.abbreviation
    : initials(campCollaborationDisplayName(campCollaboration, null, false))
}
