/**
 * Returns a display name for a user
 */
export default function (user) {
  if (user._meta?.loading) {
    return ''
  }

  return user.displayName
}
