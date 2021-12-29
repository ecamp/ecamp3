<template>
  <v-row
    no-gutters class="mx-2 mb-2">
    <v-col>
      <e-date-picker
        v-model="mappedScheduleEntry.startTimeUTCFormatted"
        value-format="YYYY-MM-DDTHH:mm:ssZ"
        :name="$tc('components.activity.createScheduleEntries.fields.startTime')"
        vee-rules="required"
        :allowed-dates="allowedStartDates"
        :filled="false"
        required />
    </v-col>
    <v-col>
      <e-time-picker
        v-model="mappedScheduleEntry.startTimeUTCFormatted"
        :name="$tc('components.activity.createScheduleEntries.fields.startTime')"
        vee-rules="required"
        :filled="false"
        required />
    </v-col>
    <v-col class="ml-4">
      <e-date-picker
        v-model="mappedScheduleEntry.endTimeUTCFormatted"
        value-format="YYYY-MM-DDTHH:mm:ssZ"
        input-class="ml-2"
        :name="$tc('components.activity.createScheduleEntries.fields.endTime')"
        vee-rules="required"
        :allowed-dates="allowedEndDates"
        :filled="false"
        required />
    </v-col>
    <v-col>
      <e-time-picker
        v-model="mappedScheduleEntry.endTimeUTCFormatted"
        input-class="ml-2"
        :name="$tc('components.activity.createScheduleEntries.fields.endTime')"
        vee-rules="required"
        :filled="false"
        required />
    </v-col>
    <!--
        <v-col>
          <e-text-field
            :value="duration(scheduleEntry.length)"
            readonly
            input-class="ml-2"
            :name="$tc('components.activity.createScheduleEntries.fields.duration')"
            :filled="false"
            icon=""
            required />
        </v-col> -->
  </v-row>
</template>
<script>
import { defineHelpers } from '@/common/helpers/scheduleEntry/dateHelperUTCFormatted.js'
import dayjs from '@/common/helpers/dayjs.js'

export default {
  name: 'CreateActivityScheduleEntriesItem',
  props: {
    // scheduleEntry to display
    scheduleEntry: {
      type: Object,
      required: true
    },

    // List of available periods
    periods: {
      type: Array,
      required: true
    }
  },
  computed: {
    mappedScheduleEntry () {
      return defineHelpers(this.scheduleEntry)
    },

    // detect selected period based on start date
    period () {
      const startDate = dayjs.utc(this.mappedScheduleEntry.startTimeUTCFormatted)

      return this.periods.find((period) => {
        return startDate.isBetween(dayjs.utc(period.start), dayjs.utc(period.end), 'date', '[]')
      })
    }
  },
  methods: {
    // returns true for any date that is within any available period
    allowedStartDates: function (val) {
      const calendarDate = dayjs.utc(val)

      return this.periods.some((period) => {
        return calendarDate.isBetween(dayjs.utc(period.start), dayjs.utc(period.end), 'date', '[]')
      })
    },

    // if a startDate is selected, returns true only for dates within the period of the selected startDate
    allowedEndDates: function (val) {
      if (this.period === undefined) {
        return this.allowedStartDates(val)
      }

      const calendarDate = dayjs.utc(val)
      return calendarDate.isBetween(dayjs.utc(this.period.start), dayjs.utc(this.period.end), 'date', '[]')
    },

    duration (length) {
      const hours = Math.floor(length / 60)
      const minutes = length % 60
      return `${hours}h` + (minutes === 0 ? '' : ` ${minutes}min`)
    },
    defineHelpers
  }
}
</script>
<style scoped lang="scss">

</style>
