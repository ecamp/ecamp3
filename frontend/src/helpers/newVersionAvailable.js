import { ref } from 'vue'
import * as Sentry from '@sentry/browser'

const newVersionAvailable = ref(false)

export function useNewVersionAvailable() {
  return newVersionAvailable
}

export function notifyNewVersionAvailable() {
  Sentry.captureMessage('In route chunk loading failed after deployment.')
  newVersionAvailable.value = true
}

export function updateToNewVersion() {
  window.location.reload()
}

export function dismissNewVersion() {
  newVersionAvailable.value = false
}
