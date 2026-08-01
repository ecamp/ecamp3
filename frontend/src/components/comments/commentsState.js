import { reactive } from 'vue'

export const commentsState = reactive({ open: false })

export function resetCommentsState() {
  commentsState.open = false
}
