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

export function filterComments(comments, statusFilter = 'open', retainedThreads = null) {
  return statusFilter === 'all'
    ? comments
    : comments.filter((comment) => {
        if (statusFilter === 'resolved') return isResolved(comment)
        const root = typeof comment.parent === 'function' ? comment.parent() : comment
        return !isResolved(comment) || retainedThreads?.has(root._meta.self)
      })
}

export function scopedComments(
  comments,
  activity,
  includeActivityComments,
  statusFilter = 'open'
) {
  const shown = filterComments(comments.items, statusFilter)
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
