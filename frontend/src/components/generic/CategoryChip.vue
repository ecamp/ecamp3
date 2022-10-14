<template>
  <v-chip dark :color="cat.color" v-bind="$attrs" v-on="$listeners">
    <div :style="{ color: textColor }">
      <slot>
        {{ cat.short }}
      </slot>
    </div>
  </v-chip>
</template>

<script>
export default {
  name: 'CategoryChip',
  props: {
    scheduleEntry: {
      type: Object,
      required: false,
      default: null,
    },
    category: {
      type: Object,
      required: false,
      default: null,
    },
  },
  computed: {
    cat() {
      return this.category || this.scheduleEntry.activity().category()
    },
    textColor() {
      const c = this.cat.color
      const r = parseInt(c.substr(1, 2), 16)
      const g = parseInt(c.substr(3, 2), 16)
      const b = parseInt(c.substr(5, 2), 16)

      const brightness = Math.round((r * 299 + g * 587 + b * 114) / 1000)
      return brightness > 125 ? 'black' : 'white'
    },
  },
}
</script>
