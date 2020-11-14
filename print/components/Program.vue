<template>
  <v-row no-gutters>
    <v-col cols="12">
      <div class="page_break">
        <h1>Program</h1>

        <div v-if="camp.periods()._meta.loading">
          <div v-for="period in periods" :key="'period_' + period.id">
            {{ period.id }} // {{ period.name }}
          </div>
        </div>
        <!--
        <activity
          v-for="activity in activities"
          :key="'activity_' + activity.id"
          :activity="activity"
        />-->
      </div>
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
  computed: {
    activities() {
      return this.camp.activities().items
    },
    periods() {
      return this.camp.periods().items
    },
  },
  mounted() {
    console.log(this.camp)
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
