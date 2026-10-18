import { describe, expect, it } from 'vitest'
import { commentCountsByActivity } from '@/components/comments/commentCounts.js'
import { scopedComments } from '@/components/comments/scopedComments.js'
import { isResolved } from '@/components/comments/commentThreads.js'

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
    expect(scopedComments({ items }, null, true, true)).toHaveLength(5)
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
