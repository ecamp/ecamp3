import { dateLong, rangeShort } from '@/../common/helpers/dateHelperUTCFormatted.js'

export const dateHelperUTCFormatted = {
  methods: {
    dateLong(dateTimeString) {
      return dateLong(dateTimeString, this.$tc.bind(this))
    },
    rangeShort(start, end) {
      return rangeShort(start, end, this.$tc.bind(this))
    },
  },
}
