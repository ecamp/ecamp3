import Vue from 'vue'

function defineHelpers (scheduleEntry, timed = false) {
  if (!Object.prototype.hasOwnProperty.call(scheduleEntry, 'startTimeUTCFormatted')) {
    Object.defineProperties(scheduleEntry, {
      startTimeUTCFormatted: {
        get () {
          return Vue.dayjs.utc(this.period().start, Vue.dayjs.HTML5_FMT.DATE).add(this.periodOffset, 'm').format()
        },
        set (value) {
          this.periodOffset = Vue.dayjs.utc(value).diff(Vue.dayjs.utc(this.period().start, Vue.dayjs.HTML5_FMT.DATE), 'm')
        }
      },
      endTimeUTCFormatted: {
        get () {
          return Vue.dayjs.utc(this.period().start, Vue.dayjs.HTML5_FMT.DATE).add(this.periodOffset + this.length, 'm').format()
        },
        set (value) {
          this.length = Vue.dayjs.utc(value).diff(Vue.dayjs.utc(this.period().start, Vue.dayjs.HTML5_FMT.DATE), 'm') - this.periodOffset
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
