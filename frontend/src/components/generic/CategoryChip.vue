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
import { parseHexColor, contrastColor } from '@/common/helpers/colors.js'

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
      return contrastColor(...parseHexColor(this.cat.color))
    },
  },
}
</script>
