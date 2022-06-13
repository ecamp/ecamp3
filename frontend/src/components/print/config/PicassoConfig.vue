<template>
  <div>
    <e-select v-model="options.periods" :items="periods" multiple />
    <e-select v-model="options.orientation" :items="orientations" />
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
}
</script>
