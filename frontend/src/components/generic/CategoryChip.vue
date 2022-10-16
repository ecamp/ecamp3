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
import bgHexToTextColor from '@/common/helpers/hexStrBgToTextColor.js'

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
      return bgHexToTextColor(this.cat.color)
    },
  },
}
</script>
