import userDisplayName from './userDisplayName.js'
import initials from './initials.js'

/**
 * Returns two characters to display for a camp collaboration based on its user
 */
export default function (campCollaboration) {
  if (!campCollaboration) {
    return ''
  }

  if (campCollaboration?.abbreviation) {
    return campCollaboration.abbreviation
  }

  if (typeof campCollaboration.user === 'function') {
    if (campCollaboration.user().abbreviation) {
      return campCollaboration.user().abbreviation
    }
    return initials(userDisplayName(campCollaboration.user()))
  }

  return initials(campCollaboration.inviteEmail || '')
}
