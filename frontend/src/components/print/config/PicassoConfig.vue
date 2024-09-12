<template>
  <div>
    <e-select
      v-model="options.periods"
      :label="$tc('print.config.periods')"
      :items="periods"
      multiple
      :filled="false"
      @input="$emit('input')"
    />
    <e-select
      v-model="options.orientation"
      :label="$tc('components.print.config.picassoConfig.orientation')"
      :items="orientations"
      :filled="false"
      @input="$emit('input')"
    />
  </div>
</template>

<script>
import cloneDeep from 'lodash/cloneDeep'

const ORIENTATIONS = [
  {
    value: 'L',
    text: 'Landscape',
  },
  {
    value: 'P',
    text: 'Portrait',
  },
]

export default {
  name: 'PicassoConfig',
  props: {
    value: { type: Object, required: true },
    camp: { type: Object, required: true },
  },
  data() {
    return {
      orientations: cloneDeep(ORIENTATIONS),
    }
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
      orientation: 'L',
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
    if (!ORIENTATIONS.map((o) => o.value).includes(config.options.orientation)) {
      config.options.orientation = 'L'
    }
    return config
  },
}
</script>
