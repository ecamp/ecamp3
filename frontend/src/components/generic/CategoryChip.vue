<template>
  <v-chip
    v-if="!loading"
    :color="cat.color"
    :text-color="textColor"
    :class="{ 'v-chip--dense': dense }"
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
    /**
     * Inline aligns chip and reduces height
     */
    dense: {
      type: Boolean,
      default: false,
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
      return contrastColor(new Color(this.cat.color)).toString({ format: 'hex' })
    },
  },
}
</script>

<style lang="scss">
.v-chip.v-chip--dense {
  font-size: 1em;
  height: 1.25em;
  vertical-align: baseline;
  padding-inline: 0.5em;
  font-weight: 600;

  .v-chip__content {
    font-size: 0.75em;
  }

  &::before {
    content: '\200B';
    font-size: 1em;
    line-height: 1;
    position: static;
  }

  &.v-size--large {
    height: 1.2em;

    .v-chip__content {
      font-size: 0.8em;
    }
  }
}
</style>
