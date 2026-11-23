import { describe, expect, it, vi } from 'vitest'
import { useClipboardEntity } from '../useClipboardEntity.js'

function deferred() {
  let resolve, reject
  const promise = new Promise((res, rej) => {
    resolve = res
    reject = rej
  })
  return { promise, resolve, reject }
}

function entity(self) {
  const loaded = { _meta: { self } }
  loaded._meta.load = Promise.resolve(loaded)
  return loaded
}

function setup() {
  const fetches = {}
  const onEntityLoaded = vi.fn()
  const onEntityLoadFailed = vi.fn()
  const clipboard = useClipboardEntity({
    fetchClipboardEntity: (url) => {
      fetches[url] = deferred()
      return fetches[url].promise
    },
    onEntityLoaded,
    onEntityLoadFailed,
  })
  return { clipboard, fetches, onEntityLoaded, onEntityLoadFailed }
}

describe('useClipboardEntity', () => {
  it('ignores a failure of an outdated url that settles after the latest url', async () => {
    const { clipboard, fetches, onEntityLoaded, onEntityLoadFailed } = setup()
    const outdated = clipboard.setClipboardEntityUrl('/camps/3c')
    const latest = clipboard.setClipboardEntityUrl('/camps/3c79b99ab424')

    fetches['/camps/3c79b99ab424'].resolve(entity('/camps/3c79b99ab424'))
    await latest
    fetches['/camps/3c'].reject('not found')
    await outdated

    expect(clipboard.clipboardEntity.value._meta.self).toBe('/camps/3c79b99ab424')
    expect(clipboard.clipboardEntityLoading.value).toBe(false)
    expect(onEntityLoaded).toHaveBeenCalledOnce()
    expect(onEntityLoadFailed).not.toHaveBeenCalled()
  })

  it('ignores an entity of an outdated url that loads after the latest url', async () => {
    const { clipboard, fetches, onEntityLoaded, onEntityLoadFailed } = setup()
    const outdated = clipboard.setClipboardEntityUrl('/camps/a')
    const latest = clipboard.setClipboardEntityUrl('/camps/b')

    fetches['/camps/b'].reject('not found')
    await latest
    fetches['/camps/a'].resolve(entity('/camps/a'))
    await outdated

    expect(clipboard.clipboardEntity.value).toBeNull()
    expect(clipboard.clipboardEntityLoading.value).toBe(false)
    expect(onEntityLoaded).not.toHaveBeenCalled()
    expect(onEntityLoadFailed).toHaveBeenCalledExactlyOnceWith('/camps/b')
  })
})
