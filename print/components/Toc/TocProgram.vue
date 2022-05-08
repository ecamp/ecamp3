<template>
  <li>
    <div class="toc-element-level-1">
      {{ $tc('print.program.title') }}
    </div>
    <ul>
      <toc-program-period
        v-for="period in periods"
        :key="period._meta.self"
        :period="period"
        :index="index"
      />
    </ul>
  </li>
</template>

<script>
export default {
  name: 'TocProgram',
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
    ])

    this.periods = this.options.periods.map((periodUri) => {
      return this.$api.get(periodUri)
    })
  },
}
</script>
