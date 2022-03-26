<template>
  <v-row no-gutters>
    <v-col cols="12">
      <div v-if="showDailySummary" class="tw-break-after-page">
        <h1>Daily summary of day {{ day.dayOffset }}</h1>
      </div>

      <div v-if="showActivities">
        <schedule-entry
          v-for="scheduleEntry in scheduleEntries"
          :key="scheduleEntry.id"
          :schedule-entry="scheduleEntry"
        />
      </div>
    </v-col>
  </v-row>
</template>

<script>
export default {
  props: {
    day: { type: Object, required: true },
    showDailySummary: { type: Boolean, required: true },
    showActivities: { type: Boolean, required: true },
  },
  data() {
    return {
      scheduleEntries: null,
    }
  },
  async fetch() {
    this.scheduleEntries = (await this.day.scheduleEntries()._meta.load).items
  },
  computed: {
    dayAsDate() {
      return this.day.dayOffset
    },
  },
}
</script>
