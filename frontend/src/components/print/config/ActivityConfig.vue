<template>
  <div class="px-md-4">
    <e-select
      v-if="!loading"
      :v-model="optionsScheduleEntry.activity ? optionsScheduleEntry : null"
      path="optionsScheduleEntry"
      :items="scheduleEntries"
      :label="$t('components.print.config.activityConfig.activity')"
      variant="underlined"
      @update:model-value="$emit('update:modelValue', modelValue)"
    />
    <v-skeleton-loader v-else type="image" height="56" />
  </div>
</template>

<script>
export default {
  name: 'ActivityConfig',
  props: {
    modelValue: { type: Object, required: true },
    camp: { type: Object, required: true },
  },
  emits: ['update:modelValue'],
  computed: {
    options: {
      get() {
        return this.modelValue
      },
      set(v) {
        this.$emit('update:modelValue', v)
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
          text: (se.number ? '(' + se.number + ') ' : '') + se.activity().title,
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
  design: {
    multiple: false,
  },
}
</script>
