<template>
  <v-container>
    <v-row no-gutters class="mx-2 mb-2">
      <v-col cols="5">
        <e-date-picker
          v-model="localScheduleEntry.start"
          value-format="YYYY-MM-DDTHH:mm:ssZ"
          :name="$tc('components.program.formScheduleEntryItem.start')"
          vee-id="startDate"
          vee-rules="required"
          :allowed-dates="allowedStartDates"
          :filled="false"
          class="float-left date-picker"
          required
        />

        <e-time-picker
          v-model="localScheduleEntry.start"
          :name="$tc('components.program.formScheduleEntryItem.start')"
          vee-id="startDatetime"
          vee-rules="required"
          :filled="false"
          class="float-left mt-0 ml-3 time-picker"
          required
        />
      </v-col>
      <v-col cols="1" class="text-center pt-4"> - </v-col>
      <v-col cols="5">
        <e-date-picker
          v-model="localScheduleEntry.end"
          value-format="YYYY-MM-DDTHH:mm:ssZ"
          :name="$tc('components.program.formScheduleEntryItem.end')"
          vee-id="endDate"
          vee-rules="required|greaterThanOrEqual_date:@startDate"
          :allowed-dates="allowedEndDates"
          :filled="false"
          class="float-left date-picker"
          required
        />

        <e-time-picker
          v-model="localScheduleEntry.end"
          :name="$tc('components.program.formScheduleEntryItem.end')"
          vee-id="endDatetime"
          :vee-rules="endTimeValidation"
          :filled="false"
          class="float-left mt-0 ml-3 time-picker"
          required
        />
      </v-col>

      <v-col cols="1" class="pt-3 text-center">
        <button-delete v-if="!isLastItem" icon-only @click="$emit('delete')" />
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
    isLastItem: {
      type: Boolean,
      required: true,
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
    endTimeValidation() {
      let validator = {
        required: true,
      }

      // only compare time if date is the same day
      if (
        this.$date
          .utc(this.localScheduleEntry.start)
          .isSame(this.$date.utc(this.localScheduleEntry.end), 'day')
      ) {
        validator.greaterThan_time = {
          min: this.$date.utc(this.localScheduleEntry.start).format('HH:mm'),
        }
      }
      return validator
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
    // returns true for any date that is within any available period
    allowedStartDates: function (val) {
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

    // if a startDate is selected, returns true only for dates within the period of the selected startDate
    allowedEndDates: function (val) {
      if (this.period === undefined) {
        return this.allowedStartDates(val)
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
  width: 120px;
}

.time-picker {
  width: 80px;
}
</style>
