<template>
  <div>
    <e-select
      v-model="options.periods"
      :items="periods"
      :label="$tc('print.config.periods')"
      multiple
      :filled="false"
      @input="$emit('input')"
    />
  </div>
</template>

<script>
import SummaryConfig from '@/components/print/config/SummaryConfig.vue'
import { SUMMARY_CONTENTTYPES } from '@/components/print/config/SummaryConfig.vue'

export default {
  name: 'StoryConfig',
  extends: SummaryConfig,
  defaultOptions() {
    return {
      periods: [],
      contentType: 'Storycontext',
    }
  },
  design: {
    multiple: false,
  },
  repairConfig(config, camp) {
    if (!config.options) config.options = {}
    if (!config.options.periods) config.options.periods = []
    if (!config.options.contentType) config.options.contentType = 'Storycontext'
    const knownPeriods = camp.periods().items.map((p) => p._meta.self)
    config.options.periods = config.options.periods.filter((period) => {
      return knownPeriods.includes(period)
    })
    if (!SUMMARY_CONTENTTYPES.includes(config.options.contentType)) {
      config.options.contentType = 'Storycontext'
    }
    return config
  },
}
</script>
