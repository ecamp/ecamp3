import {
  dateShort,
  dateLong,
  hourShort,
  hourLong,
  timeDurationShort,
  rangeShort,
  rangeLongEnd,
  dateRange,
} from '@/common/helpers/dateHelperUTCFormatted.js'

export const dateHelperUTCFormatted = {
  methods: {
    dateShort(dateTimeString) {
      return dateShort(dateTimeString, this.$tc.bind(this))
    },
    dateLong(dateTimeString) {
      return dateLong(dateTimeString, this.$tc.bind(this))
    },
    hourShort(dateTimeString) {
      return hourShort(dateTimeString, this.$tc.bind(this))
    },
    hourLong(dateTimeString) {
      return hourLong(dateTimeString, this.$tc.bind(this))
    },
    timeDurationShort(start, end) {
      return timeDurationShort(start, end, this.$tc.bind(this))
    },
    rangeShort(start, end) {
      return rangeShort(start, end, this.$tc.bind(this))
    },
    rangeLongEnd(start, end) {
      return rangeLongEnd(start, end, this.$tc.bind(this))
    },
    dateRange(start, end) {
      return dateRange(start, end, this.$tc.bind(this))
    },
  },
}
