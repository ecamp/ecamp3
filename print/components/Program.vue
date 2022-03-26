<template>
  <v-row no-gutters>
    <v-col cols="12">
      <div class="tw-break-after-page">
        <h1>Detail program for Period {{ period.description }}</h1>
      </div>

      <program-period
        v-for="period in periods"
        :key="'period_' + period.id"
        :show-daily-summary="dayOverview"
        :show-activities="showActivities"
        :period="period"
      />
    </v-col>
  </v-row>
</template>

<script>
export default {
  props: {
    camp: { type: Object, required: true },
    dayOverview: { type: Boolean, required: true },
    showActivities: { type: Boolean, required: true },
  },
  data() {
    return {
      periods: null,
    }
  },
  async fetch() {
    this.periods = (await this.camp.periods()._meta.load).items
  },
}
</script>
