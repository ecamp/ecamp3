import idToColor from './idToColor.js'

/**
 * Returns a color for a camp collaboration based on its user id and status
 */
export default function (campCollaboration) {
  if (
    campCollaboration._meta?.loading ||
    (campCollaboration.user !== null && campCollaboration.user()._meta?.loading)
  ) {
    return 'hsl(0,0,30%)'
  }

  return idToColor(
    campCollaboration.user === null ? campCollaboration.id : campCollaboration.user().id,
    campCollaboration.status === 'inactive'
  )
}
