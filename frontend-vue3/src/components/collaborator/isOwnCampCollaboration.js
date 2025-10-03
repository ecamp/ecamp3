/**
 * @typedef Collaborator {
 *   user: () => { id: string },
 * }
 *
 * @typedef Auth {
 *   user: { id: string },
 * }
 *
 * @param {Collaborator} collaborator
 * @param {Auth} auth
 * @returns {boolean}
 */
export default function isOwnCampCollaboration(collaborator, auth) {
  if (!(typeof collaborator.user === 'function')) {
    return false
  }
  return auth.user?.id === collaborator.user().id
}
