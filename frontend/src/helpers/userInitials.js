import userDisplayName from './userDisplayName.js'
import initials from './initials.js'

/**
 * Returns two characters to display for a user
 */
export default function (user) {
  if (!user) {
    return ''
  }
  if (user.abbreviation) {
    return user.abbreviation
  }
  return initials(userDisplayName(user))
}
