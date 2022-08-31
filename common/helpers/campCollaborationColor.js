import idToColor from './idToColor.js'

/**
 * Returns a color for a camp collaboration based on its user id and status
 */
export default function (campCollaboration) {
  if (!campCollaboration) {
    return idToColor('', true)
  }

  const loading = campCollaboration._meta?.loading ||
    (typeof campCollaboration.user === 'function' && campCollaboration.user()._meta?.loading)

  return idToColor(
    typeof campCollaboration.user === 'function' ? campCollaboration.user().id : campCollaboration.id,
    campCollaboration.status === 'inactive' || loading
  )
}
