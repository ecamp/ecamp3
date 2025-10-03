import {
  dateShort,
  dateLong,
  hourShort,
  hourLong,
  timeDurationShort,
  rangeTime,
  rangeShort,
  rangeLongEnd,
  dateRange,
} from '@/common/helpers/dateHelperUTCFormatted.js'

export const dateHelperUTCFormatted = {
  methods: {
    dateShort(dateTimeString) {
      return dateShort(dateTimeString, this.$t.bind(this))
    },
    dateLong(dateTimeString) {
      return dateLong(dateTimeString, this.$t.bind(this))
    },
    hourShort(dateTimeString) {
      return hourShort(dateTimeString, this.$t.bind(this))
    },
    hourLong(dateTimeString) {
      return hourLong(dateTimeString, this.$t.bind(this))
    },
    timeDurationShort(start, end) {
      return timeDurationShort(start, end, this.$t.bind(this))
    },
    rangeTime(start, end) {
      return rangeTime(start, end, this.$t.bind(this))
    },
    rangeShort(start, end) {
      return rangeShort(start, end, this.$t.bind(this))
    },
    rangeLongEnd(start, end) {
      return rangeLongEnd(start, end, this.$t.bind(this))
    },
    dateRange(start, end) {
      return dateRange(start, end, this.$t.bind(this))
    },
  },
}
