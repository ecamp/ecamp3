import { dateLong, rangeShort } from '@/common/helpers/dateHelperUTCFormatted.js'

export const dateHelperUTCFormatted = {
  methods: {
    dateLong(dateTimeString) {
      return dateLong(dateTimeString, this.$t.bind(this))
    },
    rangeShort(start, end) {
      return rangeShort(start, end, this.$t.bind(this))
    },
  },
}
