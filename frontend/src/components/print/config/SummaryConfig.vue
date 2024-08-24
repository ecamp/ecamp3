<template>
  <div>
    <e-select
      v-model="options.periods"
      :items="periods"
      :label="$tc('components.print.config.summaryConfig.periods')"
      multiple
      :filled="false"
      @input="$emit('input')"
    />
    <e-select
      v-model="options.contentType"
      :items="translatedContentTypes"
      :label="$tc('components.print.config.summaryConfig.contentType')"
      :filled="false"
      @input="$emit('input')"
    />
    <e-text-field
      v-model="options.instanceNameFilter"
      :label="$tc('components.print.config.summaryConfig.instanceNameFilter')"
      :filled="false"
      clearable
      @input="$emit('input')"
    />
  </div>
</template>

<script>
import camelCase from 'lodash/camelCase.js'

const SUMMARY_CONTENTTYPES = [
  'Storycontext',
  'Notes',
  'SafetyConsiderations',
  'LearningObjectives',
  'LearningTopics',
]

export default {
  name: 'SummaryConfig',
  props: {
    value: { type: Object, required: true },
    camp: { type: Object, required: true },
  },
  data() {
    return {
      contentTypes: SUMMARY_CONTENTTYPES,
    }
  },
  computed: {
    translatedContentTypes() {
      return this.contentTypes.map((contentType) => ({
        text: this.$tc(`contentNode.${camelCase(contentType)}.name`),
        value: contentType,
      }))
    },
    options: {
      get() {
        return this.value
      },
      set(v) {
        this.$emit('input', v)
      },
    },
    periods() {
      return this.camp.periods().items.map((p) => ({
        value: p._meta.self,
        text: p.description,
      }))
    },
  },
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
