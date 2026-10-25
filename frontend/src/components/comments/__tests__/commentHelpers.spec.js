import { describe, expect, it } from 'vitest'
import {
  commentCountsByActivity,
  scopedComments,
  isResolved,
} from '@/components/comments/commentHelpers.js'

const activity = { _meta: { self: '/activities/1' } }

function comment(id, { on = activity, parent = null, resolvedAt = null } = {}) {
  return {
    _meta: { self: `/comments/${id}` },
    resolvedAt,
    ...(on ? { activity: () => on } : {}),
    ...(parent ? { parent: () => parent } : {}),
  }
}

const openRoot = comment(1)
const openReply = comment(2, { parent: openRoot })
const resolvedRoot = comment(3, { resolvedAt: '2026-08-01T12:00:00+00:00' })
const resolvedReply = comment(4, { parent: resolvedRoot })
const resolvedCampLevel = comment(5, {
  on: null,
  resolvedAt: '2026-08-01T12:00:00+00:00',
})
const items = [openRoot, openReply, resolvedRoot, resolvedReply, resolvedCampLevel]

describe('commentThreads', () => {
  it('treats a reply as resolved when its root is', () => {
    expect(isResolved(resolvedReply)).toBe(true)
    expect(isResolved(openReply)).toBe(false)
  })
})

describe('scopedComments', () => {
  it('leaves out every comment of a resolved thread by default', () => {
    expect(scopedComments({ items }, activity, false).map((c) => c._meta.self)).toEqual([
      '/comments/1',
      '/comments/2',
    ])
  })

  it('includes resolved threads when asked to', () => {
    expect(scopedComments({ items }, null, true, 'all')).toHaveLength(5)
  })

  it('shows resolved threads with their replies for an activity', () => {
    expect(scopedComments({ items }, activity, false, 'resolved')).toEqual([
      resolvedRoot,
      resolvedReply,
    ])
  })

  it('shows only resolved camp comments in desktop camp scope', () => {
    expect(scopedComments({ items }, null, false, 'resolved')).toEqual([
      resolvedCampLevel,
    ])
  })

  it('includes resolved activity and camp comments in mobile camp scope', () => {
    expect(scopedComments({ items }, null, true, 'resolved')).toEqual([
      resolvedRoot,
      resolvedReply,
      resolvedCampLevel,
    ])
  })

  it('applies the resolved filter in camp scope too', () => {
    expect(scopedComments({ items }, null, false)).toEqual([])
  })
})

describe('commentCountsByActivity', () => {
  it('counts the comments of open threads only', () => {
    expect(commentCountsByActivity(items).get('/activities/1')).toBe(2)
  })

  it('counts nothing for an activity whose threads are all resolved', () => {
    expect(commentCountsByActivity([resolvedRoot, resolvedReply]).size).toBe(0)
  })
})
