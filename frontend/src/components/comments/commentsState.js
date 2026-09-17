import { reactive } from 'vue'

export const commentsState = reactive({ open: false, activityFilter: null })

export function openCommentsForActivity(activity) {
  commentsState.activityFilter = activity
  commentsState.open = true
}

export function clearActivityFilter() {
  commentsState.activityFilter = null
}

export function resetCommentsState() {
  commentsState.open = false
  commentsState.activityFilter = null
}
