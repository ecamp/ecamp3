<template>
  <div class="px-md-4 flex-grow-1 d-flex flex-column justify-content-between">
    <e-select
      v-model="options.periods"
      :items="periods"
      :label="$t('print.config.periods')"
      multiple
      :filled="false"
      :readonly="periods.length === 1"
      @input="$emit('input')"
    />
    <div class="flex-grow-1"></div>
    <DialogScheduleEntryFilter
      :camp="camp"
      :filter-fn="filterFn()"
      :filter="options.filter"
      hide-period-filter
      @input="updateFilter"
    />
  </div>
</template>

<script>
import SummaryConfig, {
  SUMMARY_CONTENTTYPES,
} from '@/components/print/config/SummaryConfig.vue'
import DialogScheduleEntryFilter from './DialogScheduleEntryFilter.vue'
import { repairPrintFilterConfig } from '../repairPrintConfig.js'

export default {
  name: 'SafetyConsiderationsConfig',
  components: { DialogScheduleEntryFilter },
  extends: SummaryConfig,
  defaultOptions(camp) {
    return {
      periods:
        camp.periods().items.length === 1 ? [camp.periods().items[0]._meta.self] : [],
      contentType: 'SafetyConsiderations',
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
    return repairPrintFilterConfig(config, camp, knownPeriods)
  },
}
</script>
