<template>
  <v-container>
    <v-row no-gutters class="mx-2 mb-2" justify="center" align="center">
      <v-col cols="5">
        <e-date-picker
          v-model="localScheduleEntry.start"
          value-format="YYYY-MM-DDTHH:mm:ssZ"
          path="startDate"
          vee-rules="required"
          :allowed-dates="dateIsInAnyPeriod"
          :filled="false"
          class="float-left date-picker mr-3 mt-1"
          required
        />

        <e-time-picker
          v-model="localScheduleEntry.start"
          path="startDatetime"
          vee-rules="required"
          :filled="false"
          class="float-left mt-1 time-picker"
          required
        />
      </v-col>
      <v-col cols="1" class="text-center pt-4">
        <v-icon>mdi-ray-start-arrow</v-icon>
      </v-col>
      <v-col cols="5">
        <e-date-picker
          v-model="localScheduleEntry.end"
          value-format="YYYY-MM-DDTHH:mm:ssZ"
          path="endDate"
          vee-rules="required|greaterThanOrEqual_date:@startDate"
          :min="localScheduleEntry.start"
          :allowed-dates="dateIsInSelectedPeriod"
          :filled="false"
          class="float-left date-picker mr-3 mt-1"
          required
        />

        <e-time-picker
          v-model="localScheduleEntry.end"
          path="endDatetime"
          :vee-rules="endTimeValidation"
          :min="minEndTime"
          :filled="false"
          class="float-left mt-1 time-picker"
          required
        />
      </v-col>

      <v-col cols="1" class="pt-3 text-center">
        <button-delete v-if="deletable" icon-only @click="$emit('delete')" />
      </v-col>
    </v-row>
  </v-container>
</template>
<script>
import dayjs from '@/common/helpers/dayjs.js'

import ButtonDelete from '@/components/buttons/ButtonDelete.vue'

export default {
  name: 'FormScheduleEntryItem',
  components: { ButtonDelete },
  provide() {
    return {
      entityName: 'scheduleEntry',
    }
  },
  props: {
    // scheduleEntry to display
    scheduleEntry: {
      type: Object,
      required: true,
    },

    // List of available periods
    periods: {
      type: Array,
      required: true,
    },

    // true if current item is the last scheduleEntry
    deletable: {
      type: Boolean,
      required: false,
    },
  },
  data() {
    return {
      localScheduleEntry: this.scheduleEntry,
    }
  },
  computed: {
    // detect selected period based on start date
    period() {
      const startDate = dayjs.utc(this.localScheduleEntry.start)

      return this.periods.find((period) => {
        return startDate.isBetween(
          dayjs.utc(period.start),
          dayjs.utc(period.end),
          'date',
          '[]'
        )
      })
    },
    isSameDay() {
      return this.$date
        .utc(this.localScheduleEntry.start)
        .isSame(this.$date.utc(this.localScheduleEntry.end), 'day')
    },
    endTimeValidation() {
      const validator = {
        required: true,
      }

      // only compare time if date is the same day
      if (this.isSameDay) {
        validator.greaterThan_time = {
          min: this.$date.utc(this.localScheduleEntry.start),
        }
      }
      return validator
    },
    minEndTime() {
      if (!this.isSameDay) return null
      return this.$date
        .utc(this.localScheduleEntry.start)
        .add(this.$date.duration(15, 'm'))
        .format('HH:mm')
    },
  },
  watch: {
    'period._meta.self': function (value) {
      if (value === undefined || this.period === undefined) return

      const period = this.period

      // change period in object
      this.localScheduleEntry.period = () => period
    },

    // watch start and automatically shift end if start changes (=keep duration)
    'localScheduleEntry.start': function (newValue, oldValue) {
      const delta = dayjs.utc(newValue).diff(dayjs.utc(oldValue))
      this.localScheduleEntry.end = dayjs
        .utc(this.localScheduleEntry.end)
        .add(delta)
        .format()
    },
  },
  methods: {
    dateIsInAnyPeriod: function (val) {
      const calendarDate = dayjs.utc(val)
      return this.periods.some((period) => {
        return calendarDate.isBetween(
          dayjs.utc(period.start),
          dayjs.utc(period.end),
          'date',
          '[]'
        )
      })
    },
    dateIsInSelectedPeriod: function (val) {
      if (this.period === undefined) {
        return this.dateIsInAnyPeriod(val)
      }
      const calendarDate = dayjs.utc(val)
      return calendarDate.isBetween(
        dayjs.utc(this.period.start),
        dayjs.utc(this.period.end),
        'date',
        '[]'
      )
    },
  },
}
</script>
<style scoped lang="scss">
.date-picker {
  width: 130px;
}

.time-picker {
  width: 115px;
}
</style>
