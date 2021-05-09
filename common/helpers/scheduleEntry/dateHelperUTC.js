import dayjs from '../dayjs.js'
import { HTML5_FMT } from '../dateFormat.js'

function defineHelpers (scheduleEntry, timed = false) {
  if (!Object.prototype.hasOwnProperty.call(scheduleEntry, 'startTime')) {
    Object.defineProperties(scheduleEntry, {
      startTime: {
        get () {
          return dayjs.utc(this.period().start, HTML5_FMT.DATE).add(this.periodOffset, 'm').valueOf()
        },
        set (value) {
          this.periodOffset = dayjs.utc(value).diff(dayjs.utc(this.period().start, HTML5_FMT.DATE), 'm')
        }
      },
      endTime: {
        get () {
          return dayjs.utc(this.period().start, HTML5_FMT.DATE).add(this.periodOffset + this.length, 'm').valueOf()
        },
        set (value) {
          this.length = dayjs.utc(value).diff(dayjs.utc(this.period().start, HTML5_FMT.DATE), 'm') - this.periodOffset
        }
      }
    })
  }
  if (timed) {
    Object.defineProperty(scheduleEntry, 'timed', {
      value: true
    })
  }
  return scheduleEntry
}

export {
  defineHelpers
}
