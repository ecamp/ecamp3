<template>
  <div>
    <story
      v-for="period in periods"
      :key="period._meta.self"
      :period="period"
      :camp="camp"
      :index="index"
    />
  </div>
</template>

<script>
export default {
  name: 'ConfigStory',
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
      return this.$api.get(periodUri) // TODO prevent specifying arbitrary absolute URLs that the print container should fetch...
    })
  },
}
</script>
