const RELOAD_PATH_KEY = 'ecamp3:chunkReloadPath'

export function isChunkLoadError(error) {
  const message = error?.message ?? ''
  const chromiumMessage = /Failed to fetch dynamically imported module/i
  if (chromiumMessage.test(message)) {
    return true
  }
  const firefoxMessage = /error loading dynamically imported module/i
  if (firefoxMessage.test(message)) {
    return true
  }
  const safariMessage = /Importing a module script failed/i
  // noinspection RedundantIfStatementJS
  if (safariMessage.test(message)) {
    return true
  }
  return false
}

export function reloadOnChunkLoadError(error, to) {
  if (!isChunkLoadError(error)) {
    return false
  }

  const targetPath =
    to?.fullPath ??
    window.location.pathname + window.location.search + window.location.hash

  if (window.sessionStorage.getItem(RELOAD_PATH_KEY) === targetPath) {
    return false
  }

  window.sessionStorage.setItem(RELOAD_PATH_KEY, targetPath)
  window.location.assign(targetPath)
  return true
}

export function clearChunkReloadGuard() {
  window.sessionStorage.removeItem(RELOAD_PATH_KEY)
}
