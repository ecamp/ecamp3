<template>
  <div>
    <e-select
      v-model="options.periods"
      :items="periods"
      :label="$tc('components.print.config.storyConfig.periods')"
      multiple
      :filled="false"
      @input="$emit('input')"
    />
  </div>
</template>

<script>
export default {
  name: 'StoryConfig',
  props: {
    value: { type: Object, required: true },
    camp: { type: Object, required: true },
  },
  data() {
    return {}
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
    }
  },
  design: {
    multiple: false,
  },
  repairConfig(config, camp) {
    if (!config.options) config.options = {}
    if (!config.options.periods) config.options.periods = []
    const knownPeriods = camp.periods().items.map((p) => p._meta.self)
    config.options.periods = config.options.periods.filter((period) => {
      return knownPeriods.includes(period)
    })
    return config
  },
}
</script>
