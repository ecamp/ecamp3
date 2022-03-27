<template>
  <li>
    <div class="toc-element-level-1">
      {{ $tc('print.program.title') }}
    </div>
    <ul>
      <toc-program-period
        v-for="(periodUri, idx) in options.periods"
        :key="idx"
        :period="getPeriod(periodUri)"
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
