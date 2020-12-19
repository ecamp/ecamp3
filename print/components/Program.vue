<template>
  <v-row no-gutters>
    <v-col cols="12">
      <div class="page_break">
        <h1>Detail program</h1>
      </div>

      <program-period
        v-for="period in periods"
        :key="'period_' + period.id"
        :show-daily-summary="showDailySummary"
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
    showDailySummary: { type: Boolean, required: true },
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

<style lang="scss" scoped>
@media print {
  .page_break {
    page-break-after: always;
  }
}
</style>
