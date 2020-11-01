<template>
  <div class="e-form-container">
    <v-card v-for="scheduleEntry in mappedScheduleEntries"
            :key="scheduleEntry.id"
            outlined
            color="grey lighten-3" class="period mb-2 rounded-b-0">
      <v-row no-gutters>
        <v-col>
          <legend class="pa-2">
            {{ $tc('components.activity.createScheduleEntries.name') }}
          </legend>
        </v-col>
      </v-row>
      <v-row no-gutters class="mx-2 mb-2">
        <v-col>
          <e-time-picker
            v-model="scheduleEntry.startTime"
            :name="$tc('components.activity.createScheduleEntries.fields.startTime')"
            vee-rules="required"
            :my="2"
            :filled="false"
            required />
        </v-col>
        <v-col>
          <e-time-picker
            v-model="scheduleEntry.endTime"
            input-class="ml-2"
            :name="$tc('components.activity.createScheduleEntries.fields.endTime')"
            vee-rules="required"
            :my="2"
            :filled="false"
            icon=""
            required />
        </v-col>
      </v-row>
    </v-card>
  </div>
</template>
<script>
import { defineHelpers } from '@/plugins/scheduleEntries'

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
