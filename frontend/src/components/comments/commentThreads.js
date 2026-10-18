/**
 * @param {Object} comment
 * @return {boolean} whether the thread the comment belongs to is resolved
 */
export function isResolved(comment) {
  return typeof comment.parent === 'function'
    ? !!comment.parent().resolvedAt
    : !!comment.resolvedAt
}
