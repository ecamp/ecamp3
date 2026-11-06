/**
 * Returns the legal name for a user
 */
export default function (user) {
  if (!user || !user.profile() || user.profile()._meta.loading) return ''
  return user.profile().legalName || ''
}
