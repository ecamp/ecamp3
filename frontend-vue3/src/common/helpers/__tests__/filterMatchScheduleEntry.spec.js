import { describe, expect, it } from "vitest";
import { filterMatchScheduleEntry } from '../filterMatchScheduleEntry.js'

const scheduleEntry = {
  period: () => ({ _meta: { self: '/periods/1a2b3c4d' } }),
  day: () => ({ _meta: { self: '/days/1a2b3c4d' } }),
  activity: () => ({
    category: () => ({ _meta: { self: '/categories/1a2b3c4d' } }),
    activityResponsibles: () => ({
      items: [
        { campCollaboration: () => ({ _meta: { self: '/camp_collaborations/1a2b3c4d' } }) },
        { campCollaboration: () => ({ _meta: { self: '/camp_collaborations/ffffffff' } }) },
      ]
    }),
    progressLabel: () => ({ _meta: { self: '/progress_labels/1a2b3c4d' } })
  }),
}

describe('filterMatchScheduleEntry', () => {
  it.each([
    [null, true],
    [undefined, true],
    ['wrong input', true],
    [{}, true],
    [{ period: undefined }, true],
    [{ period: null }, true],
    [{ period: '/periods/1a2b3c4d' }, true],
    [{ period: '/periods/00000000' }, false],
    [{ period: ['/periods/1a2b3c4d'] }, false], // an array of periods is currently not supported
    [{ day: undefined }, true],
    [{ day: null }, true],
    [{ day: '/days/1a2b3c4d' }, true],
    [{ day: '/days/00000000' }, false],
    [{ day: ['/days/1a2b3c4d'] }, true],
    [{ day: ['/days/00000000'] }, false],
    [{ day: ['/days/1a2b3c4d', '/days/00000000'] }, true],
    [{ day: ['/days/00000000', '/days/1a2b3c4d'] }, true],
    [{ category: undefined }, true],
    [{ category: null }, true],
    [{ category: '/categories/1a2b3c4d' }, true],
    [{ category: '/categories/00000000' }, false],
    [{ category: ['/categories/1a2b3c4d'] }, true],
    [{ category: ['/categories/00000000'] }, false],
    [{ category: ['/categories/1a2b3c4d', '/categories/00000000'] }, true],
    [{ category: ['/categories/00000000', '/categories/1a2b3c4d'] }, true],
    [{ responsible: undefined }, true],
    [{ responsible: null }, true],
    [{ responsible: ['/camp_collaborations/1a2b3c4d'] }, true],
    [{ responsible: ['/camp_collaborations/00000000'] }, false],
    [{ responsible: ['/camp_collaborations/1a2b3c4d', '/camp_collaborations/00000000'] }, false],
    [{ responsible: ['/camp_collaborations/00000000', '/camp_collaborations/1a2b3c4d'] }, false],
    [{ responsible: ['/camp_collaborations/1a2b3c4d', '/camp_collaborations/ffffffff'] }, true],
    [{ responsible: ['/camp_collaborations/ffffffff', '/camp_collaborations/1a2b3c4d'] }, true],
    [{ progressLabel: undefined }, true],
    [{ progressLabel: null }, true],
    [{ progressLabel: '/progress_labels/1a2b3c4d' }, true],
    [{ progressLabel: '/progress_labels/00000000' }, false],
    [{ progressLabel: ['/progress_labels/1a2b3c4d'] }, true],
    [{ progressLabel: ['/progress_labels/00000000'] }, false],
    [{ progressLabel: ['/progress_labels/1a2b3c4d', '/progress_labels/00000000'] }, true],
    [{ progressLabel: ['/progress_labels/00000000', '/progress_labels/1a2b3c4d'] }, true],
    [{
      period: '/periods/1a2b3c4d',
      day: ['/days/1a2b3c4d'],
      category: ['/categories/1a2b3c4d'],
      responsible: ['/camp_collaborations/1a2b3c4d'],
      progressLabel: ['/progress_labels/1a2b3c4d'],
    }, true],
    [{
      period: null,
      day: ['/days/1a2b3c4d'],
      category: ['/categories/1a2b3c4d'],
      responsible: ['/camp_collaborations/1a2b3c4d'],
      progressLabel: ['/progress_labels/1a2b3c4d'],
    }, true],
    [{
      period: '/periods/1a2b3c4d',
      day: null,
      category: ['/categories/1a2b3c4d'],
      responsible: ['/camp_collaborations/1a2b3c4d'],
      progressLabel: ['/progress_labels/1a2b3c4d'],
    }, true],
    [{
      period: '/periods/1a2b3c4d',
      day: ['/days/1a2b3c4d'],
      category: null,
      responsible: ['/camp_collaborations/1a2b3c4d'],
      progressLabel: ['/progress_labels/1a2b3c4d'],
    }, true],
    [{
      period: '/periods/1a2b3c4d',
      day: ['/days/1a2b3c4d'],
      category: ['/categories/1a2b3c4d'],
      responsible: null,
      progressLabel: ['/progress_labels/1a2b3c4d'],
    }, true],
    [{
      period: '/periods/1a2b3c4d',
      day: ['/days/1a2b3c4d'],
      category: ['/categories/1a2b3c4d'],
      responsible: ['/camp_collaborations/1a2b3c4d'],
      progressLabel: null,
    }, true],
    [{
      period: '/periods/00000000',
      day: ['/days/1a2b3c4d'],
      category: ['/categories/1a2b3c4d'],
      responsible: ['/camp_collaborations/1a2b3c4d'],
      progressLabel: ['/progress_labels/1a2b3c4d'],
    }, false],
    [{
      period: '/periods/1a2b3c4d',
      day: ['/days/00000000'],
      category: ['/categories/1a2b3c4d'],
      responsible: ['/camp_collaborations/1a2b3c4d'],
      progressLabel: ['/progress_labels/1a2b3c4d'],
    }, false],
    [{
      period: '/periods/1a2b3c4d',
      day: ['/days/1a2b3c4d'],
      category: ['/categories/00000000'],
      responsible: ['/camp_collaborations/1a2b3c4d'],
      progressLabel: ['/progress_labels/1a2b3c4d'],
    }, false],
    [{
      period: '/periods/1a2b3c4d',
      day: ['/days/1a2b3c4d'],
      category: ['/categories/1a2b3c4d'],
      responsible: ['/camp_collaborations/00000000'],
      progressLabel: ['/progress_labels/1a2b3c4d'],
    }, false],
    [{
      period: '/periods/1a2b3c4d',
      day: ['/days/1a2b3c4d'],
      category: ['/categories/1a2b3c4d'],
      responsible: ['/camp_collaborations/1a2b3c4d'],
      progressLabel: ['/progress_labels/00000000'],
    }, false],
  ])('maps %o to %s', (filter, expected) => {
    expect(filterMatchScheduleEntry(scheduleEntry, filter)).toEqual(expected)
  })
})
