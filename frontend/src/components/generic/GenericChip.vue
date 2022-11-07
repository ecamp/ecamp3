<template>
  <v-chip v-bind="$attrs" :class="{ 'v-chip--dense': dense }" v-on="$listeners">
    <slot />
  </v-chip>
</template>

<script>
export default {
  name: 'GenericChip',
  props: {
    /**
     * Inline aligns chip and reduces height
     */
    dense: {
      type: Boolean,
      default: false,
    },
  },
}
</script>

<style scoped lang="scss">
.v-chip--dense {
  font-size: 1em;
  height: 1.25em;
  vertical-align: baseline;
  padding-inline: 0.5em;

  ::v-deep(.v-chip__content) {
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

    ::v-deep(.v-chip__content) {
      font-size: 0.8em;
    }
  }
}
</style>
