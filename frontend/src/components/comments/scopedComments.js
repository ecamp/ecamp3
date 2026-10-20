import { isResolved } from '@/components/comments/commentThreads.js'

/**
 * The comments the panel shows for a scope.
 *
 * Activity scope shows that activity's comments. Camp scope shows the comments that belong
 * to no activity - the camp-level ones and the orphans - and adds the activity comments
 * only where the panel is the whole screen anyway, because elsewhere a comment is read
 * next to the activity it is about.
 *
 * @param {Object} comments the camp's comment collection
 * @param {?Object} activity the activity the panel is scoped to, if any
 * @param {boolean} includeActivityComments whether camp scope also lists activity comments
 * @param {boolean} includeResolved whether resolved threads are listed too
 * @return {Array}
 */
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
