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
        <!-- TODO: add selector for day -->

        <v-col>
          <e-time-picker
            v-model="scheduleEntry.startTimeUTCFormatted"
            :name="$tc('components.activity.createScheduleEntries.fields.startTime')"
            vee-rules="required"
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
            icon=""
            required />
        </v-col>
        <v-col>
          <e-text-field
            :value="duration(scheduleEntry.length)"
            readonly
            input-class="ml-2"
            :name="$tc('components.activity.createScheduleEntries.fields.duration')"
            :filled="false"
            icon=""
            required />
        </v-col>
      </v-row>
    </v-card>
  </div>
</template>
<script>
import { defineHelpers } from '@/common/helpers/scheduleEntry/dateHelperUTCFormatted.js'

export default {
  name: 'CreateActivityScheduleEntries',
  props: {
    scheduleEntries: {
      type: Array,
      required: true
    }
  },
  computed: {
    mappedScheduleEntries () {
      return this.scheduleEntries.map((entry) => defineHelpers(entry))
    }
  },
  methods: {
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
