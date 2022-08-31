import { i18n } from '@/plugins/i18n' // this imports i18-plugin from "frontend" or from "print", depending on where the helper is used
import userDisplayName from './userDisplayName.js'

/**
 * Returns a display name for a camp collaboration based on its status
 */
export default function (campCollaboration, indicateInactive = true) {
  if (!campCollaboration) {
    return ''
  }

  let text = typeof campCollaboration.user === 'function'
    ? userDisplayName(campCollaboration.user())
    : (campCollaboration.inviteEmail || '')

  if (campCollaboration.status === 'inactive' && indicateInactive) {
    text += ' (' + i18n.tc('entity.campCollaboration.inactive') + ')'
  }

  return text
}
