export function isResolved(comment) {
  return typeof comment.parent === 'function'
    ? !!comment.parent().resolvedAt
    : !!comment.resolvedAt
}

export function commentCountsByActivity(comments) {
  const counts = new Map()
  comments.forEach((comment) => {
    if (typeof comment.activity !== 'function' || isResolved(comment)) return
    const uri = comment.activity()._meta.self
    counts.set(uri, (counts.get(uri) ?? 0) + 1)
  })
  return counts
}

export function scopedComments(
  comments,
  activity,
  includeActivityComments,
  includeResolved = false
) {
  const shown = includeResolved
    ? comments.items
    : comments.items.filter((comment) => !isResolved(comment))
  if (activity) {
    return shown.filter(
      (comment) =>
        typeof comment.activity === 'function' &&
        comment.activity()._meta.self === activity._meta.self
    )
  }
  if (includeActivityComments) return shown
  return shown.filter((comment) => typeof comment.activity !== 'function')
}
