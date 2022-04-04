<template>
  <div class="tw-break-after-page">
    <div>
      <h1 class="tw-text-2xl tw-font-bold tw-mb-6">
        {{ $tc('print.program.title') }}: {{ $tc('entity.period.name') }}
        {{ period.description }}
      </h1>
    </div>

    <program-day
      v-for="day in days"
      :key="'day' + day.id"
      :day="day"
      :show-daily-summary="showDailySummary"
      :show-activities="showActivities"
      :index="index"
    />
  </div>
</template>

<script>
export default {
  props: {
    period: { type: Object, required: true },
    showDailySummary: { type: Boolean, required: true },
    showActivities: { type: Boolean, required: true },
    index: { type: Number, required: true },
  },
  data() {
    return {
      days: null,
    }
  },
  async fetch() {
    await Promise.all([
      this.period.days().$loadItems(),
      this.period.scheduleEntries().$loadItems(),
      this.$api
        .get()
        .contentNodes({ period: this.period._meta.self })
        .$loadItems(),
    ])

    this.days = this.period.days().items
  },
}
</script>
