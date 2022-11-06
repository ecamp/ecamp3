<!--
Displays a single activity
-->

<template>
  <v-container fluid>
    <ScheduleEntry :schedule-entry="scheduleEntry" />
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
    scheduleEntry: {
      type: Function,
      required: true,
    },
  },
}
</script>
