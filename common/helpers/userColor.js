import idToColor from './idToColor.js'

/**
 * Returns a color for a user based on their id
 */
export default function (user) {
  return idToColor(user.id, user._meta?.loading)
}
