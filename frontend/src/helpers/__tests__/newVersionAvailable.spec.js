import { afterEach, beforeEach, describe, expect, it, vi } from 'vitest'
import {
  dismissNewVersion,
  notifyNewVersionAvailable,
  updateToNewVersion,
  useNewVersionAvailable,
} from '@/helpers/newVersionAvailable.js'

describe('newVersionAvailable', () => {
  beforeEach(() => {
    dismissNewVersion()
  })

  afterEach(() => {
    dismissNewVersion()
    vi.unstubAllGlobals()
  })

  it('is not available by default', () => {
    expect(useNewVersionAvailable().value).toBe(false)
  })

  it('becomes available after notifying', () => {
    notifyNewVersionAvailable()
    expect(useNewVersionAvailable().value).toBe(true)
  })

  it('shares the same reactive state across calls', () => {
    const a = useNewVersionAvailable()
    const b = useNewVersionAvailable()
    notifyNewVersionAvailable()
    expect(a.value).toBe(true)
    expect(b.value).toBe(true)
  })

  it('is dismissable so the user can continue working', () => {
    notifyNewVersionAvailable()
    dismissNewVersion()
    expect(useNewVersionAvailable().value).toBe(false)
  })

  it('reloads the page to update', () => {
    const reload = vi.fn()
    vi.stubGlobal('window', { location: { reload } })
    updateToNewVersion()
    expect(reload).toHaveBeenCalledOnce()
  })
})
