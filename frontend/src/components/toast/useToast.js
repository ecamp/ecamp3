import { computed, shallowReactive } from 'vue'

const DEFAULT_TIMEOUT = 5000
const MAX_VISIBLE_TOASTS = 2

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

function dismissToast(id) {
  const index = toasts.findIndex((toast) => toast.id === id)
  if (index !== -1) {
    toasts.splice(index, 1)
  }
}

function clearToasts() {
  toasts.splice(0)
}

const visibleToasts = computed(() => toasts.slice(0, MAX_VISIBLE_TOASTS).reverse())

const toast = {
  error(content, options) {
    return addToast('error', content, options)
  },
  info(content, options) {
    return addToast('info', content, options)
  },
}

function useToast() {
  return toast
}

export { clearToasts, dismissToast, MAX_VISIBLE_TOASTS, toasts, useToast, visibleToasts }
