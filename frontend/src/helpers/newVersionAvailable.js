import { ref } from 'vue'

const newVersionAvailable = ref(false)

export function useNewVersionAvailable() {
  return newVersionAvailable
}

export function notifyNewVersionAvailable() {
  newVersionAvailable.value = true
}

export function updateToNewVersion() {
  window.location.reload()
}

export function dismissNewVersion() {
  newVersionAvailable.value = false
}
