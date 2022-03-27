<template>
  <div>
    <picasso
      v-for="(periodUri, idx) in options.periods"
      :key="idx"
      :period="getPeriod(periodUri)"
      :camp="camp"
      :orientation="options.orientation"
    />
  </div>
</template>

<script>
export default {
  name: 'ConfigPicasso',
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
