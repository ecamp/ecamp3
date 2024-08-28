import { describe, expect, it } from 'vitest'
import shortScheduleEntryDescription from '../shortScheduleEntryDescription.js'
import createI18n from '@/components/print/print-client/i18n.js'
import { i18n } from '@/plugins/i18n'

describe('shortScheduleEntryDescription', () => {
  const { translate } = createI18n(i18n.messages, 'en')
  const tc = (key, _count, params) => translate(key, params)

  it.each([
    [{ _meta: { loading: true } }, ''],
    [null, ''],
    [undefined, ''],
    [
      {
        _meta: { loading: false },
        number: '1.2',
        activity: () => ({
          title: 'foo',
        }),
        dayNumber: '3',
      },
      '1.2',
    ],
    [
      {
        _meta: { loading: false },
        number: '',
        activity: () => ({
          title: 'foo',
        }),
        dayNumber: '3',
        start: '2024-01-01T10:00:00+00:00',
      },
      'day 3 10:00',
    ],
  ])('maps %o to "%s"', (input, expected) => {
    expect(shortScheduleEntryDescription(input, tc)).toEqual(expected)
  })
})
