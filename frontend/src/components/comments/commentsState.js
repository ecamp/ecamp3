import { reactive } from 'vue'

export const RESOLVED_COMMENT_GRACE_PERIOD = 2000
const removalTimers = new Map()

export const commentsState = reactive({
  open: false,
  activityFilter: null,
  statusFilter: 'open',
  retainedThreads: new Set(),
  removalDeadlines: new Map(),
})

export function retainCommentThread(uri) {
  clearTimeout(removalTimers.get(uri))
  removalTimers.delete(uri)
  commentsState.removalDeadlines.delete(uri)
  commentsState.retainedThreads.add(uri)
}

export function releaseCommentThread(uri) {
  clearTimeout(removalTimers.get(uri))
  removalTimers.delete(uri)
  commentsState.removalDeadlines.delete(uri)
  commentsState.retainedThreads.delete(uri)
}

export function scheduleCommentThreadRemoval(uri) {
  if (!commentsState.retainedThreads.has(uri)) return
  clearTimeout(removalTimers.get(uri))
  commentsState.removalDeadlines.set(uri, Date.now() + RESOLVED_COMMENT_GRACE_PERIOD)
  removalTimers.set(
    uri,
    setTimeout(() => releaseCommentThread(uri), RESOLVED_COMMENT_GRACE_PERIOD)
  )
}

export function openCommentsForActivity(activity) {
  commentsState.activityFilter = activity
  commentsState.open = true
}

export function clearActivityFilter() {
  commentsState.activityFilter = null
}

export function resetCommentsState() {
  for (const uri of commentsState.retainedThreads) releaseCommentThread(uri)
  commentsState.open = false
  commentsState.activityFilter = null
  commentsState.statusFilter = 'open'
}
