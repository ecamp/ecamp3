import { computed, ref } from 'vue'

export function useClipboardEntity({
  fetchClipboardEntity,
  onEntityLoaded,
  onEntityLoadFailed,
}) {
  const clipboardPermission = ref('unknown')
  const clipboardEntity = ref(null)
  const _clipoardEntityUrl = ref(null)
  const loading = ref(false)

  const clipboardEntityUrl = computed({
    get() {
      return _clipoardEntityUrl.value
    },
    set: setClipboardEntityUrl,
  })

  async function setClipboardEntityUrl(url) {
    loading.value = true
    _clipoardEntityUrl.value = url

    return fetchClipboardEntity(url)
      .then((entityProxy) => {
        if (entityProxy == null) return Promise.reject('not found')
        return entityProxy?._meta.load
      })
      .then((entity) => {
        clipboardEntity.value = entity
        onEntityLoaded(entity)
        loading.value = false
      })
      .catch(() => {
        clipboardEntity.value = null
        onEntityLoadFailed(url)
        loading.value = false
      })
  }

  const clipboardAccessDenied = computed(() => {
    return (
      clipboardPermission.value === 'unaccessible' ||
      clipboardPermission.value === 'denied'
    )
  })

  const showClipboardPrompt = computed(() => clipboardPermission.value === 'prompt')

  const hasClipboardEntity = computed(() => {
    return clipboardEntity.value != null && clipboardEntity.value._meta.self != null
  })

  const attemptLoadingEntityFromClipboard = async () => {
    clipboardEntity.value = null
    return navigator.permissions
      .query({ name: 'clipboard-read' })
      .then(async (p) => {
        clipboardPermission.value = p.state
        if (p.state === 'granted') {
          const url = await navigator.clipboard.readText()
          if (url) return setClipboardEntityUrl(url)
        }
        // If we get here, we have failed to get the url from the clipboard
        onEntityLoadFailed(null)
      })
      .catch(() => {
        clipboardPermission.value = 'unaccessible'
        console.warn('clipboard permission not requestable')
      })
  }

  const clearClipboard = async () => {
    await navigator.clipboard.writeText('')
    attemptLoadingEntityFromClipboard()
  }

  return {
    clipboardAccessDenied,
    showClipboardPrompt,
    hasClipboardEntity,
    clipboardEntity,
    clipboardEntityUrl,
    clipboardEntityLoading: loading,
    attemptLoadingEntityFromClipboard,
    setClipboardEntityUrl,
    clearClipboard,
  }
}
