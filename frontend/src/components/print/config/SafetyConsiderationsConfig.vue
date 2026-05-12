<template>
  <div class="px-md-4 flex-grow-1 d-flex flex-column justify-content-between">
    <e-select
      v-model="options.periods"
      :items="periods"
      path="periods"
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
import SummaryConfig, {
  SUMMARY_CONTENTTYPES,
} from '@/components/print/config/SummaryConfig.vue'
import DialogScheduleEntryFilter from './DialogScheduleEntryFilter.vue'
import repairFilterConfig from '../../program/repairFilterConfig.js'

export default {
  name: 'SafetyConsiderationsConfig',
  components: { DialogScheduleEntryFilter },
  extends: SummaryConfig,
  emits: ['update:modelValue'],
  defaultOptions(camp) {
    return {
      periods:
        camp.periods().items.length === 1 ? [camp.periods().items[0]._meta.self] : [],
      contentType: 'SafetyConsiderations',
      filter: repairFilterConfig(null, camp),
    }
  },
  design: {
    multiple: false,
  },
  repairConfig(config, camp) {
    if (!config.options) config.options = {}
    if (!config.options.contentType) config.options.contentType = 'SafetyConsiderations'
    if (!SUMMARY_CONTENTTYPES.includes(config.options.contentType)) {
      config.options.contentType = 'SafetyConsiderations'
    }
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
