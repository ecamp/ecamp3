/**
 * The comments the panel shows for a scope, and the same list the toggle button counts.
 *
 * Activity scope shows that activity's comments. Camp scope shows the comments that belong
 * to no activity - the camp-level ones and the orphans - and adds the activity comments
 * only where the panel is the whole screen anyway, because elsewhere a comment is read
 * next to the activity it is about.
 *
 * @param {Object} comments the camp's comment collection
 * @param {?Object} activity the activity the panel is scoped to, if any
 * @param {boolean} includeActivityComments whether camp scope also lists activity comments
 * @return {Array}
 */
export function scopedComments(comments, activity, includeActivityComments) {
  if (activity) {
    return comments.items.filter(
      (comment) =>
        typeof comment.activity === 'function' &&
        comment.activity()._meta.self === activity._meta.self
    )
  }
  if (includeActivityComments) return comments.items
  return comments.items.filter((comment) => typeof comment.activity !== 'function')
}
