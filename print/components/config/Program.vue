<template>
  <div class="tw-break-after-page">
    <program-period
      v-for="(periodUri, idx) in options.periods"
      :key="idx"
      :period="getPeriod(periodUri)"
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
    return {}
  },
  async fetch() {
    await this.$api.get(this.camp.periods)._meta.load
  },
  methods: {
    getPeriod(uri) {
      return this.$api.get(uri)
    },
  },
}
</script>
