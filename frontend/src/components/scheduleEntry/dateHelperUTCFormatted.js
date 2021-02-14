function defineHelpers (dayjs, scheduleEntry, timed = false) {
  if (!Object.prototype.hasOwnProperty.call(scheduleEntry, 'startTimeUTCFormatted')) {
    Object.defineProperties(scheduleEntry, {
      startTimeUTCFormatted: {
        get () {
          return dayjs.utc(this.period().start, dayjs.HTML5_FMT.DATE).add(this.periodOffset, 'm').format()
        },
        set (value) {
          this.periodOffset = dayjs.utc(value).diff(dayjs.utc(this.period().start, dayjs.HTML5_FMT.DATE), 'm')
        }
      },
      endTimeUTCFormatted: {
        get () {
          return dayjs.utc(this.period().start, dayjs.HTML5_FMT.DATE).add(this.periodOffset + this.length, 'm').format()
        },
        set (value) {
          this.length = dayjs.utc(value).diff(dayjs.utc(this.period().start, dayjs.HTML5_FMT.DATE), 'm') - this.periodOffset
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
