<template>
  <div class="e-form-container">
    <v-card outlined
            color="grey lighten-3" class="period mb-2 rounded-b-0">
      <v-row no-gutters>
        <v-col>
          <legend class="pa-2">
            {{ $tc('components.activity.createScheduleEntries.name') }}
          </legend>
        </v-col>
      </v-row>
      <v-row v-for="scheduleEntry in mappedScheduleEntries"
             :key="scheduleEntry.id"
             no-gutters class="mx-2 mb-2">
        <v-col>
          <e-date-picker
            v-model="scheduleEntry.startTimeUTCFormatted"
            value-format="YYYY-MM-DDTHH:mm:ssZ"
            :name="$tc('components.activity.createScheduleEntries.fields.startTime')"
            vee-rules="required"
            :allowed-dates="allowedStartDates"
            :filled="false"
            required />
        </v-col>
        <v-col>
          <e-time-picker
            v-model="scheduleEntry.startTimeUTCFormatted"
            :name="$tc('components.activity.createScheduleEntries.fields.startTime')"
            vee-rules="required"
            :filled="false"
            required />
        </v-col>
        <v-col class="ml-4">
          <e-date-picker
            v-model="scheduleEntry.endTimeUTCFormatted"
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
            v-model="scheduleEntry.endTimeUTCFormatted"
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
    </v-card>
  </div>
</template>
<script>
import { defineHelpers } from '@/common/helpers/scheduleEntry/dateHelperUTCFormatted.js'
import dayjs from '@/common/helpers/dayjs.js'

export default {
  name: 'CreateActivityScheduleEntries',
  props: {
    scheduleEntries: {
      type: Array,
      required: true
    },
    periods: {
      type: Array,
      required: true
    }
  },
  computed: {
    mappedScheduleEntries () {
      return this.scheduleEntries.map((entry) => defineHelpers(entry))
    },

    // detect selected period based on start date
    period () {
      const startDate = dayjs.utc(this.mappedScheduleEntries[0].startTimeUTCFormatted)

      return this.periods.find((period) => {
        return startDate.isBetween(period.start, period.end, 'date', '[]')
      })
    }
  },
  methods: {
    allowedStartDates: function (val) {
      const calendarDate = dayjs.utc(val)

      return this.periods.some((period) => {
        return calendarDate.isBetween(period.start, period.end, 'date', '[]')
      })
    },
    allowedEndDates: val => parseInt(val.split('-')[2], 10) % 2 === 0,
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
.period.period {
  border-bottom-width: 1px !important;
  border-bottom-style: solid !important;
  border-bottom-color: rgba(0, 0, 0, 0.42) !important;
}
</style>
