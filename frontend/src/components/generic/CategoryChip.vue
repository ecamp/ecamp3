<template>
  <GenericChip
    v-if="!loading"
    :color="cat.color"
    :text-color="textColor"
    v-bind="$attrs"
    v-on="$listeners"
  >
    <slot>
      <span class="d-sr-only">
        {{ cat.name }}
      </span>
      <span aria-hidden="true">
        {{ cat.short }}
      </span>
    </slot>
    <slot name="after" />
  </GenericChip>
  <GenericChip v-else v-bind="$attrs" v-on="$listeners"
    ><GenericIcon inherit spin>mdi-loading</GenericIcon></GenericChip
  >
</template>

<script>
import { contrastColor } from '@/common/helpers/colors.js'
import GenericChip from '@/components/generic/GenericChip.vue'
import GenericIcon from '@/components/generic/GenericIcon.vue'

export default {
  name: 'CategoryChip',
  components: { GenericIcon, GenericChip },
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
    loading() {
      return this.cat?._meta?.loading
    },
    cat() {
      return this.category || this.scheduleEntry.activity().category()
    },
    textColor() {
      return contrastColor(this.cat.color)
    },
  },
}
</script>

<style scoped>
.v-chip {
  font-weight: 500;
}
</style>
