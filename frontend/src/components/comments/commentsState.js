import { reactive } from 'vue'

export const commentsState = reactive({ open: false, focusedActivity: null })

export function focusActivityComments(activityUri) {
  commentsState.open = true
  clearCommentsFocus()
  requestAnimationFrame(() =>
    requestAnimationFrame(() => {
      commentsState.focusedActivity = activityUri
    })
  )
}

export function clearCommentsFocus() {
  commentsState.focusedActivity = null
}

export function resetCommentsState() {
  commentsState.open = false
  commentsState.focusedActivity = null
}
