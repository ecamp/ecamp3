<template>
  <v-row no-gutters>
    <v-col cols="12">
      <div class="tw-break-after-page">
        <h1>Period {{ period.description }}</h1>
      </div>

      <program-day
        v-for="day in days"
        :key="'day' + day.id"
        :day="day"
        :show-daily-summary="showDailySummary"
        :show-activities="showActivities"
      />
    </v-col>
  </v-row>
</template>

<script>
export default {
  props: {
    period: { type: Object, required: true },
    showDailySummary: { type: Boolean, required: true },
    showActivities: { type: Boolean, required: true },
  },
  data() {
    return {
      days: null,
    }
  },
  async fetch() {
    this.days = (await this.period.days()._meta.load).items
  },
}
</script>
