import { beforeEach, describe, expect, it } from 'vitest'
import {
  clearToasts,
  dismissToast,
  MAX_VISIBLE_TOASTS,
  useToast,
  visibleToasts,
} from '@/components/toast/useToast.js'

describe('useToast', () => {
  beforeEach(() => {
    clearToasts()
  })

  it('adds info and error messages with their severity and timeout', () => {
    const toast = useToast()

    const infoId = toast.info('Copied', { timeout: 2000 })
    const errorId = toast.error('Saving failed')

    expect(visibleToasts.value).toEqual([
      {
        id: errorId,
        type: 'error',
        content: 'Saving failed',
        timeout: 30000,
      },
      {
        id: infoId,
        type: 'info',
        content: 'Copied',
        timeout: 2000,
      },
    ])
  })

  it('shows at most two messages and queues the rest', () => {
    const toast = useToast()

    const firstId = toast.info('First')
    toast.info('Second')
    toast.info('Third')

    expect(visibleToasts.value).toHaveLength(MAX_VISIBLE_TOASTS)
    expect(visibleToasts.value.map(({ content }) => content)).toEqual(['Second', 'First'])

    dismissToast(firstId)

    expect(visibleToasts.value.map(({ content }) => content)).toEqual(['Third', 'Second'])
  })
})
