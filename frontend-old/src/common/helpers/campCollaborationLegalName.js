import userLegalName from './userLegalName.js'

/**
 * Returns a legal name for a camp collaboration based on its status
 */
export default function (campCollaboration) {
  if (!campCollaboration) {
    return ''
  }

  return typeof campCollaboration.user === 'function'
    ? userLegalName(campCollaboration.user())
    : ''
}
