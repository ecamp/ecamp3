<template>
  <v-chip
    v-if="!cat._meta.loading"
    :color="cat.color"
    :text-color="textColor"
    v-bind="$attrs"
    v-on="$listeners"
  >
    <slot>
      {{ cat.short }}
    </slot>
  </v-chip>
</template>

<script>
import { contrastColor } from '@/common/helpers/colors.js'
import Color from 'colorjs.io'

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
      return contrastColor(new Color(this.cat.color)).toString({ format: 'hex' })
    },
  },
}
</script>
