import userDisplayName from './userDisplayName.js'
import initials from './initials.js'

/**
 * Returns two characters to display for a camp collaboration based on its user
 */
export default function (user) {
  return initials(userDisplayName(user))
}
