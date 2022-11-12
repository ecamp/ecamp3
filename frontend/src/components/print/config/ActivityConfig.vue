<template>
  <div>
    <e-select v-if="!loading" v-model="optionsScheduleEntry" :items="scheduleEntries" />
    <v-skeleton-loader v-else type="image" height="56" />
  </div>
</template>

<script>
export default {
  name: 'ActivityConfig',
  props: {
    value: { type: Object, required: true },
    camp: { type: Object, required: true },
  },
  computed: {
    options: {
      get() {
        return this.value
      },
      set(v) {
        this.$emit('input', v)
      },
    },
    optionsScheduleEntry: {
      get() {
        return {
          activity: this.options.activity,
          scheduleEntry: this.options.scheduleEntry,
        }
      },
      set(val) {
        this.options.activity = val.activity
        this.options.scheduleEntry = val.scheduleEntry
      },
    },
    scheduleEntries() {
      let scheduleEntries = []

      this.camp.periods().items.forEach((p) => {
        const periodScheduleEntries = p.scheduleEntries().items.map((se) => ({
          value: { activity: se.activity()._meta.self, scheduleEntry: se._meta.self },
          text: '(' + se.number + ') ' + se.activity().title,
        }))
        scheduleEntries = [...scheduleEntries, ...periodScheduleEntries]
      })

      return scheduleEntries
    },
    loading() {
      return [
        this.camp.activities(),
        ...this.camp.periods().items.map((period) => period.scheduleEntries()),
      ].some((entity) => entity._meta.loading)
    },
  },
  defaultOptions() {
    return {
      activity: null,
      scheduleEntry: null,
    }
  },
}
</script>
