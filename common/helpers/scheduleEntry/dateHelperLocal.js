import dayjs from '../dayjs.js'
import { HTML5_FMT } from '../dateFormat.js'

/**
 * 
 * @param scheduleEntry entry to decorate
 * @param timed ??
 */
function defineHelpers (scheduleEntry, timed = false) {
  if (!Object.prototype.hasOwnProperty.call(scheduleEntry, 'startTime')) {
    Object.defineProperties(scheduleEntry, {
      // start time as unix (ms) timestamp
      // based on periodOffset
      startTime: {
        get () {
          return dayjs(this.period().start, HTML5_FMT.DATE).add(this.periodOffset, 'm').valueOf()
        },
        set (value) {
          this.periodOffset = dayjs(value).diff(dayjs(this.period().start, HTML5_FMT.DATE), 'm')
        }
      },

      // end time as unit (ms) timestamp
      // based on periodOffset and length
      endTime: {
        get () {
          return dayjs(this.period().start, HTML5_FMT.DATE).add(this.periodOffset + this.length, 'm').valueOf()
        },
        set (value) {
          this.length = dayjs(value).diff(dayjs(this.period().start, HTML5_FMT.DATE), 'm') - this.periodOffset
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
