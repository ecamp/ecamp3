<template>
  <div class="px-md-4 flex-grow-1 d-flex flex-column justify-content-between">
    <e-select
      v-model="options.periods"
      path="periods"
      :items="periods"
      :label="$t('print.config.periods')"
      multiple
      :variant="periods.length === 1 ? 'plain' : 'underlined'"
      :readonly="periods.length === 1"
      @update:model-value="$emit('update:modelValue', modelValue)"
    />
    <div class="flex-grow-1"></div>
    <DialogScheduleEntryFilter
      :camp="camp"
      :filter-fn="filterFn()"
      :model-value="options.filter"
      hide-period-filter
      @update:model-value="updateFilter"
    />
  </div>
</template>

<script>
import DialogScheduleEntryFilter from './DialogScheduleEntryFilter.vue'
import { filterMatchScheduleEntry } from '@/common/helpers/filterMatchScheduleEntry.js'
import repairFilterConfig from '../../program/repairFilterConfig.js'

export default {
  name: 'ActivityListConfig',
  components: { DialogScheduleEntryFilter },
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
    periods() {
      return this.camp.periods().items.map((p) => ({
        value: p._meta.self,
        text: p.description,
      }))
    },
    selectedPeriods() {
      if (!this.options.periods) return this.camp.periods().items
      return this.camp.periods().items.filter((period) => {
        return this.options.periods.includes(period._meta.self)
      })
    },
    selectedScheduleEntries() {
      return this.selectedPeriods.flatMap((period) => period.scheduleEntries().items)
    },
  },
  defaultOptions(camp) {
    return {
      periods:
        camp.periods().items.length === 1 ? [camp.periods().items[0]._meta.self] : [],
      filter: repairFilterConfig(null, camp),
    }
  },
  methods: {
    filterFn() {
      return (filter) =>
        this.selectedScheduleEntries.filter((scheduleEntry) =>
          filterMatchScheduleEntry(scheduleEntry, filter)
        )
    },
    updateFilter(newFilter) {
      this.options.filter = newFilter
      this.$emit('update:modelValue', this.options)
    },
  },
  design: {
    multiple: true,
  },
  repairConfig(config, camp) {
    if (!config.options) config.options = {}
    const knownPeriods = camp.periods().items.map((p) => p._meta.self)
    if (knownPeriods.length === 1) {
      config.options.periods = [camp.periods().items[0]._meta.self]
    } else {
      if (!config.options.periods) config.options.periods = []
      config.options.periods = config.options.periods.filter((period) => {
        return knownPeriods.includes(period)
      })
    }
    config.options.filter = repairFilterConfig(config.options.filter, camp)
    return config
  },
}
</script>
