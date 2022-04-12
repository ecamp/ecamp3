<template>
  <div class="tw-break-after-page">
    <schedule-entry :schedule-entry="scheduleEntry" :index="index" />
  </div>
</template>

<script>
export default {
  name: 'ConfigActivity',
  props: {
    options: { type: Object, required: false, default: null },
    camp: { type: Object, required: true },
    index: { type: Number, required: true },
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
      this.$api.get().contentTypes().$loadItems(),

      // might not be needed for every activity, but safer to do eager loading instead of n+1 later on
      this.camp.materialLists().$loadItems(),
      this.camp.campCollaborations().$loadItems(),
    ])

    this.scheduleEntry = scheduleEntry
  },
}
</script>
