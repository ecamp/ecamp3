<template>
  <div>
    <e-select
      v-model="options.periods"
      :label="$tc('components.print.config.picassoConfig.periods')"
      :items="periods"
      multiple
      :filled="false"
    />
    <e-select
      v-model="options.orientation"
      :label="$tc('components.print.config.picassoConfig.orientation')"
      :items="orientations"
      :filled="false"
    />
  </div>
</template>

<script>
export default {
  name: 'PicassoConfig',
  props: {
    value: { type: Object, required: true },
    camp: { type: Object, required: true },
  },
  data() {
    return {
      orientations: [
        {
          value: 'L',
          text: 'Landscape',
        },
        {
          value: 'P',
          text: 'Portrait',
        },
      ],
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
}
</script>
