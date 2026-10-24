import { afterEach, beforeEach, describe, expect, it, vi } from 'vitest'
import { enableAutoUnmount, mount } from '@vue/test-utils'
import { setupVuetify } from '/tests/setupVuetify.js'
import dayjs from '@/common/helpers/dayjs.js'
import CommentTime from '@/components/comments/CommentTime.vue'

setupVuetify()
enableAutoUnmount(afterEach)

const formats = {
  'global.datetime.dateTimeLong': 'dd DD.MM.YYYY HH:mm',
  'global.datetime.hourLong': 'HH:mm',
  'global.datetime.dateShort': 'dd D.M.',
}

function mountTime(value) {
  return mount(CommentTime, {
    props: { value },
    global: {
      mocks: {
        $t: (key, named) =>
          formats[key] ?? [key, ...Object.values(named ?? {})].join(' '),
        $date: dayjs,
      },
    },
  })
}

function localTime(value) {
  return dayjs(value).toDate()
}

function shownTime(wrapper) {
  return wrapper.find('[data-testid="comment-time"]').text()
}

describe('CommentTime', () => {
  beforeEach(() => {
    vi.useFakeTimers({ toFake: ['Date'] })
    vi.setSystemTime(localTime('2026-10-19T12:00'))
  })

  afterEach(() => {
    vi.useRealTimers()
  })

  it('names today', () => {
    const createTime = localTime('2026-10-19T09:30').toISOString()

    expect(shownTime(mountTime(createTime))).toBe(
      `components.comments.commentTime.today ${dayjs(createTime).format('HH:mm')}`
    )
  })

  it('names yesterday', () => {
    const createTime = localTime('2026-10-18T09:30').toISOString()

    expect(shownTime(mountTime(createTime))).toBe(
      `components.comments.commentTime.yesterday ${dayjs(createTime).format('HH:mm')}`
    )
  })

  it('leaves out the year for earlier days of this year', () => {
    const createTime = localTime('2026-03-12T09:30').toISOString()

    expect(shownTime(mountTime(createTime))).toBe(
      dayjs(createTime).format('dd D.M. HH:mm')
    )
  })

  it('leaves out the weekday for earlier years', () => {
    const createTime = localTime('2025-12-03T09:30').toISOString()

    expect(shownTime(mountTime(createTime))).toBe(dayjs(createTime).format('L HH:mm'))
  })

  it('offers the full date and time', () => {
    const createTime = localTime('2026-10-19T09:30').toISOString()
    const wrapper = mountTime(createTime)

    expect(wrapper.find('time').attributes('title')).toBe(
      dayjs(createTime).format('dd DD.MM.YYYY HH:mm')
    )
    expect(wrapper.find('time').attributes('datetime')).toBe(createTime)
  })
})
