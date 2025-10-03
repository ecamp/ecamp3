<!--
Displays a single activity
-->

<template>
  <v-container fluid>
    <ScheduleEntry :activity-id="activityId" :schedule-entry-id="scheduleEntryId" />
  </v-container>
</template>

<script>
import ScheduleEntry from '@/components/activity/ScheduleEntry.vue'

export default {
  name: 'Activity',
  components: {
    ScheduleEntry,
  },

  props: {
    activityId: {
      type: String,
      required: true,
    },
    scheduleEntryId: {
      type: String,
      default: null,
    },
  },
}
</script>
