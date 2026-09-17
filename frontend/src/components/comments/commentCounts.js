/**
 * Counts how many comments each activity carries. Camp-level comments and orphans,
 * which belong to no activity, are not counted.
 *
 * @param {Array} comments
 * @return {Map<string, number>}
 */
export function commentCountsByActivity(comments) {
  const counts = new Map()
  comments.forEach((comment) => {
    if (typeof comment.activity !== 'function') return
    const uri = comment.activity()._meta.self
    counts.set(uri, (counts.get(uri) ?? 0) + 1)
  })
  return counts
}
