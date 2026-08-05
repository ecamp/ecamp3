import { describe, expect, it } from 'vitest'
import { firstAppearanceByActivity } from '../firstAppearanceByActivity.js'

function scheduleEntry(activityUri) {
  return { activity: () => ({ _meta: { self: activityUri } }) }
}

const first = scheduleEntry('/activities/1')
const second = scheduleEntry('/activities/2')
const firstAgain = scheduleEntry('/activities/1')

describe('firstAppearanceByActivity', () => {
  it('returns an empty map for no schedule entries', () => {
    expect(firstAppearanceByActivity([])).toEqual(new Map())
  })

  it('positions activities by the order of the given schedule entries', () => {
    expect(firstAppearanceByActivity([second, first])).toEqual(
      new Map([
        ['/activities/2', 0],
        ['/activities/1', 1],
      ])
    )
  })

  it('positions an activity by its first schedule entry', () => {
    expect(firstAppearanceByActivity([first, second, firstAgain])).toEqual(
      new Map([
        ['/activities/1', 0],
        ['/activities/2', 1],
      ])
    )
  })

  it('repositions an activity when its first schedule entry is filtered out', () => {
    expect(firstAppearanceByActivity([second, firstAgain])).toEqual(
      new Map([
        ['/activities/2', 0],
        ['/activities/1', 1],
      ])
    )
  })

  it('leaves out an activity whose every schedule entry is filtered out', () => {
    expect(firstAppearanceByActivity([second])).toEqual(new Map([['/activities/2', 0]]))
  })
})
