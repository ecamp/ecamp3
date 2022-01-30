import i18n from '@/plugins/i18n'

/**
 * Returns a display name for a camp collaboration based on its status
 */
export default function (campCollaboration) {
  if (campCollaboration.user === null) {
    return campCollaboration.inviteEmail
  }

  let text = campCollaboration.user().displayName

  if (campCollaboration.status === 'inactive') {
    text += ' (' + i18n.tc('entity.campCollaboration.inactive') + ')'
  }

  return text
}
