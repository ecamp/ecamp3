<template>
  <div class="px-md-4 flex-grow-1 d-flex flex-column justify-content-between">
    <e-select
      v-model="options.periods"
      :label="$t('print.config.periods')"
      :items="periods"
      path="periods"
      multiple
      :variant="periods.length === 1 ? 'plain' : 'underlined'"
      :readonly="periods.length === 1"
      @update:model-value="$emit('update:modelValue', modelValue)"
    />
    <div class="e-form-container" :class="{ 'e-picasso-config__row': landscape }">
      <e-select
        v-model="options.orientation"
        :label="$t('components.print.config.picassoConfig.orientation')"
        :items="orientations"
        path="orientation"
        variant="underlined"
        class="flex-grow-1"
        @update:model-value="$emit('update:modelValue', modelValue)"
      />
      <e-select
        v-model="options.responsibles"
        :label="$t('components.print.config.picassoConfig.responsibles')"
        :items="responsiblesDisplays"
        path="responsibles"
        variant="underlined"
        class="flex-grow-1"
        @update:model-value="$emit('update:modelValue', modelValue)"
      />
    </div>
    <e-checkbox
      v-if="hasYSPhysicalActivityDuration"
      v-model="options.showYSPhysicalActivityDuration"
      path="showYSPhysicalActivityDuration"
      :label="$t('entity.scheduleEntry.fields.ysPhysicalActivityDuration')"
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
import { filterMatchScheduleEntry } from '@/helpers/filterMatchScheduleEntry.js'
import repairFilterConfig from '../../program/repairFilterConfig.js'

export default {
  name: 'PicassoConfig',
  components: { DialogScheduleEntryFilter },
  props: {
    modelValue: { type: Object, required: true },
    camp: { type: Object, required: true },
  },
  emits: ['update:modelValue'],
  data() {
    return {
      orientations: [
        {
          value: 'L',
          text: this.$t('components.print.config.picassoConfig.landscape'),
        },
        {
          value: 'P',
          text: this.$t('components.print.config.picassoConfig.portrait'),
        },
      ],
      responsiblesDisplays: [
        {
          value: 'names',
          text: this.$t('components.print.config.picassoConfig.responsiblesNames'),
        },
        {
          value: 'avatars',
          text: this.$t('components.print.config.picassoConfig.responsiblesAvatars'),
        },
      ],
    }
  },
  computed: {
    options: {
      get() {
        return this.modelValue
      },
      set(v) {
        this.$emit('update:modelValue', v)
      },
    },
    landscape() {
      return this.options.orientation === 'L'
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
    hasYSPhysicalActivityDuration() {
      return this.selectedScheduleEntries.some(
        (scheduleEntry) => scheduleEntry.ysPhysicalActivityDurationMinutes !== null
      )
    },
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
  defaultOptions(camp) {
    return {
      periods:
        camp.periods().items.length === 1 ? [camp.periods().items[0]._meta.self] : [],
      orientation: 'L',
      showYSPhysicalActivityDuration: false,
      responsibles: 'names',
      filter: repairFilterConfig(null, camp),
    }
  },
  design: {
    multiple: false,
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
    if (!['L', 'P'].includes(config.options.orientation)) {
      config.options.orientation = 'L'
    }
    if (!['names', 'avatars'].includes(config.options.responsibles)) {
      config.options.responsibles = 'names'
    }
    config.options.filter = repairFilterConfig(config.options.filter, camp)
    return config
  },
}
</script>

<style scoped>
.e-picasso-config__row {
  display: flex;
  gap: 8px;
}

.e-picasso-config__row > :deep(.e-form-container + .e-form-container) {
  margin-top: 0;
}
</style>
