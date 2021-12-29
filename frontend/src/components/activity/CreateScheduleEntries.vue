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
      <create-schedule-entries-item v-for="scheduleEntry in scheduleEntries"
                                    :key="scheduleEntry.id"
                                    :schedule-entry="scheduleEntry"
                                    :periods="periods" />
    </v-card>
  </div>
</template>
<script>
import CreateScheduleEntriesItem from './CreateScheduleEntriesItem.vue'

export default {
  name: 'CreateActivityScheduleEntries',
  components: { CreateScheduleEntriesItem },
  props: {
    scheduleEntries: {
      type: Array,
      required: true
    },
    periods: {
      type: Array,
      required: true
    }
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
