<template>
  <div class="tw-break-after-page">
    <schedule-entry :schedule-entry="scheduleEntry" />
  </div>
</template>

<script>
export default {
  name: 'ConfigActivity',
  props: {
    options: { type: Object, required: false, default: null },
    camp: { type: Object, required: true },
  },
  data() {
    return {
      scheduleEntry: null,
    }
  },
  async fetch() {
    const [scheduleEntry] = await Promise.all([
      this.$api.get(this.options.scheduleEntry)._meta.load,
      this.$api.get(this.options.activity)._meta.load,
    ])

    this.scheduleEntry = scheduleEntry
  },
}
</script>
