<template>
  <div class="tw-break-after-page">
    <generic-error-message v-if="$fetchState.error" :error="$fetchState.error" />
    <schedule-entry v-else :schedule-entry="scheduleEntry" :index="index" />
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
    if (this.options.scheduleEntry === null || this.options.activity === null) {
      throw new Error('No activity and scheduleEntry provided provided')
    }

    const [scheduleEntry] = await Promise.all([
      this.$api.get(this.options.scheduleEntry)._meta.load, // TODO prevent specifying arbitrary absolute URLs that the print container should fetch...
      this.$api.get(this.options.activity)._meta.load, // TODO prevent specifying arbitrary absolute URLs that the print container should fetch...
      this.$api.get().contentTypes().$loadItems(),

      // might not be needed for every activity, but safer to do eager loading instead of n+1 later on
      this.camp.materialLists().$loadItems(),
      this.camp.campCollaborations().$loadItems(),
    ])

    this.scheduleEntry = scheduleEntry
  },
}
</script>
