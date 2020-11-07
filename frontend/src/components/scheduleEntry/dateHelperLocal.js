function defineHelpers (scheduleEntry, timed = false) {
  if (!Object.prototype.hasOwnProperty.call(scheduleEntry, 'startTime')) {
    Object.defineProperties(scheduleEntry, {
      startTime: {
        get () {
          // need to cut-off UTC Timezone such that the date is interpreted in local timezone setting (because v-calendar has no timezone setting)
          const [periodStart] = this.period().start.split('+')

          return Date.parse(periodStart) + (this.periodOffset * 60000)
        },
        set (value) {
          // need to cut-off UTC Timezone such that the date is interpreted in local timezone setting (because v-calendar has no timezone setting)
          const [periodStart] = this.period().start.split('+')

          this.periodOffset = (value - Date.parse(periodStart)) / 60000
        }
      },
      endTime: {
        get () {
          return this.startTime + (this.length * 60000)
        },
        set (value) {
          this.length = (value - this.startTime) / 60000
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
