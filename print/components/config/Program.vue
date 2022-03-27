<template>
  <div class="tw-break-after-page">
    <program-period
      v-for="period in periods"
      :key="period._meta.self"
      :period="period"
      :camp="camp"
      :show-daily-summary="options.dayOverview || false"
      :show-activities="true"
      :index="index"
    />
  </div>
</template>

<script>
export default {
  name: 'ConfigProgram',
  props: {
    options: { type: Object, required: false, default: null },
    camp: { type: Object, required: true },
    index: { type: Number, required: true },
  },
  data() {
    return {
      periods: [],
    }
  },
  async fetch() {
    await this.camp.periods().$loadItems()

    this.periods = this.options.periods.map((periodUri) => {
      return this.$api.get(periodUri)
    })
  },
}
</script>
