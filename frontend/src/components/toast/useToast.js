import { computed, shallowReactive } from 'vue'

export const DEFAULT_TIMEOUT = 30000
export const MAX_VISIBLE_TOASTS = 2

const toasts = shallowReactive([])
let nextToastId = 0

function addToast(type, content, options = {}) {
  const id = ++nextToastId

  toasts.push({
    id,
    type,
    content,
    timeout: options.timeout ?? DEFAULT_TIMEOUT,
  })

  return id
}

export function dismissToast(id) {
  const index = toasts.findIndex((toast) => toast.id === id)
  if (index !== -1) {
    toasts.splice(index, 1)
  }
}

export function clearToasts() {
  toasts.splice(0)
}

export const visibleToasts = computed(() => toasts.slice(0, MAX_VISIBLE_TOASTS).reverse())

function error(content, options) {
  return addToast('error', content, options)
}

function info(content, options) {
  return addToast('info', content, options)
}

export function useToast() {
  return {
    error,
    info,
  }
}
