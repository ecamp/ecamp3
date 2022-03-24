<template>
  <div>
    <e-select
      v-model="options.periods"
      :items="periods"
      multiple />
    <e-checkbox v-model="options.dayOverview" :label="$tc('components.print.printConfigurator.config.ProgramConfig.dayOverview')" />
  </div>
</template>

<script>
export default {
  name: 'Program',
  props: {
    value: { type: Object, required: true },
    camp: { type: Object, required: true }
  },
  computed: {
    options: {
      get () { return this.value },
      set (v) { this.$emit('input', v) }
    },
    periods () {
      return this.camp.periods().items.map(p => ({
        value: p._meta.self,
        text: p.description
      }))
    }
  },
  defaultOptions () {
    return {
      periods: [],
      dayOverview: true
    }
  }
}
</script>
