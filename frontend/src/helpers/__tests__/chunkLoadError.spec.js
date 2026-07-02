import { afterEach, beforeEach, describe, expect, it, vi } from 'vitest'
import {
  clearChunkReloadGuard,
  isChunkLoadError,
  reloadOnChunkLoadError,
} from '@/helpers/chunkLoadError.js'

describe('isChunkLoadError', () => {
  it.each([
    'Failed to fetch dynamically imported module: https://e.camp/assets/Foo-abc123.js',
    'error loading dynamically imported module: https://e.camp/assets/Foo.js',
    'Importing a module script failed.',
  ])('detects chunk load error "%s"', (message) => {
    expect(isChunkLoadError(new Error(message))).toBe(true)
  })

  it.each([
    new Error('Something else went wrong'),
    new TypeError('Cannot read properties of undefined'),
    null,
    undefined,
    {},
  ])('ignores unrelated error %s', (error) => {
    expect(isChunkLoadError(error)).toBe(false)
  })
})

describe('reloadOnChunkLoadError', () => {
  const assign = vi.fn()
  let store = {}

  beforeEach(() => {
    assign.mockReset()
    store = {}
    vi.stubGlobal('window', {
      location: {
        assign,
        pathname: '/current',
        search: '',
        hash: '',
      },
      sessionStorage: {
        getItem: (key) => (key in store ? store[key] : null),
        setItem: (key, value) => {
          store[key] = String(value)
        },
        removeItem: (key) => {
          delete store[key]
        },
      },
    })
  })

  afterEach(() => {
    vi.unstubAllGlobals()
  })

  const chunkError = new Error('Failed to fetch dynamically imported module: x.js')

  it('reloads to the target path on a chunk load error', () => {
    const reloaded = reloadOnChunkLoadError(chunkError, { fullPath: '/camp/123' })

    expect(reloaded).toBe(true)
    expect(assign).toHaveBeenCalledWith('/camp/123')
  })

  it('does not reload for unrelated errors', () => {
    const reloaded = reloadOnChunkLoadError(new Error('boom'), { fullPath: '/camp/123' })

    expect(reloaded).toBe(false)
    expect(assign).not.toHaveBeenCalled()
  })

  it('reloads only once per target path to avoid an infinite loop', () => {
    expect(reloadOnChunkLoadError(chunkError, { fullPath: '/camp/123' })).toBe(true)
    expect(reloadOnChunkLoadError(chunkError, { fullPath: '/camp/123' })).toBe(false)
    expect(assign).toHaveBeenCalledTimes(1)
  })

  it('reloads again for a different target path', () => {
    expect(reloadOnChunkLoadError(chunkError, { fullPath: '/camp/123' })).toBe(true)
    expect(reloadOnChunkLoadError(chunkError, { fullPath: '/camp/456' })).toBe(true)
    expect(assign).toHaveBeenCalledTimes(2)
  })

  it('reloads again after the guard is cleared by a successful navigation', () => {
    expect(reloadOnChunkLoadError(chunkError, { fullPath: '/camp/123' })).toBe(true)
    clearChunkReloadGuard()
    expect(reloadOnChunkLoadError(chunkError, { fullPath: '/camp/123' })).toBe(true)
    expect(assign).toHaveBeenCalledTimes(2)
  })

  it('falls back to the current location when no target route is given', () => {
    reloadOnChunkLoadError(chunkError, undefined)

    expect(assign).toHaveBeenCalledWith('/current')
  })
})
