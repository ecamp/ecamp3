/**
 * The comments the panel shows for a scope: all of the camp's comments, or only the ones
 * on a single activity.
 *
 * @param {Object} comments the camp's comment collection
 * @param {?Object} activity the activity the panel is scoped to, if any
 * @return {Array}
 */
export function scopedComments(comments, activity) {
  if (!activity) return comments.items
  return comments.items.filter(
    (comment) =>
      typeof comment.activity === 'function' &&
      comment.activity()._meta.self === activity._meta.self
  )
}
