<template>
  <div>
    <picasso-period
      v-for="period in periods"
      :key="period._meta.self"
      :period="period"
      :camp="camp"
      :orientation="options.orientation"
      :index="index"
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
    return {
      periods: [],
    }
  },
  async fetch() {
    await Promise.all([
      this.camp.periods().$loadItems(),
      this.camp.activities().$loadItems(),
      this.camp.categories().$loadItems(),
      this.camp.campCollaborations().$loadItems(),
    ])

    this.periods = this.options.periods.map((periodUri) => {
      return this.$api.get(periodUri) // TODO prevent specifying arbitrary absolute URLs that the print container should fetch...
    })
  },
}
</script>
