import idToColor from './idToColor.js'

/**
 * Returns a color for a user based on their id
 */
export default function (user) {
  if (user._meta?.loading) {
    return 'hsl(0,0,30%)'
  }

  return idToColor(user.id)
}
