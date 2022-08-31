import userDisplayName from './userDisplayName.js'

/**
 * Returns a display name for a camp collaboration based on its status
 */
export default function (campCollaboration, tc, indicateInactive = true) {
  if (!campCollaboration) {
    return ''
  }

  let text = typeof campCollaboration.user === 'function'
    ? userDisplayName(campCollaboration.user())
    : (campCollaboration.inviteEmail || '')

  if (campCollaboration.status === 'inactive' && indicateInactive) {
    text += ' (' + tc('entity.campCollaboration.inactive') + ')'
  }

  return text
}
