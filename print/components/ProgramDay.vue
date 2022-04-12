<template>
  <div>
    <div v-if="showDailySummary" class="tw-text-2xl tw-mb-6">
      <h1>
        {{ $tc('entity.day.name') }} {{ day.number }} ({{
          dateLong(day.start)
        }})
      </h1>
    </div>

    <div v-if="showActivities">
      <schedule-entry
        v-for="scheduleEntry in scheduleEntries"
        :key="scheduleEntry.id"
        :schedule-entry="scheduleEntry"
        :index="index"
      />
    </div>
  </div>
</template>

<script>
import { dateLong } from '@/../common/helpers/dateHelperUTCFormatted.js'

export default {
  props: {
    day: { type: Object, required: true },
    showDailySummary: { type: Boolean, required: true },
    showActivities: { type: Boolean, required: true },
    index: { type: Number, required: true },
  },
  data() {
    return {}
  },
  computed: {
    // returns scheduleEntries of current day without the need for an additional API call
    scheduleEntries() {
      return this.day
        .period()
        .scheduleEntries()
        .items.filter((scheduleEntry) => {
          return scheduleEntry.day()._meta.self === this.day._meta.self
        })
    },
  },
  methods: {
    dateLong,
  },
}
</script>
