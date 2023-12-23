<template>
  <div
    class="ec-layout-nodecard"
    :class="{
      'ec-layout-nodecard--layout-mode': layoutMode,
      'elevation-4--light': layoutMode && !isRoot,
      'ec-layout-nodecard--root': isRoot,
    }"
  >
    <div
      v-if="!isRoot && layoutMode"
      class="text-center d-flex align-center px-2 py-1 justify-space-between gap-1 ec-layout-nodecard-header blue--text text--darken-2"
    >
      <slot name="header" />
    </div>
    <slot />
  </div>
</template>

<script>
export default {
  name: 'LayoutCard',
  props: {
    layoutMode: { type: Boolean, default: false },
    isRoot: { type: Boolean, default: false },
  },
}
</script>

<style scoped>
.ec-layout-nodecard--layout-mode:not(.ec-layout-nodecard--root) {
  transition: all 0.2s ease;
  transition-property: background-color, border-color, box-shadow;
  border: 1px solid rgb(13, 71, 161, 0.36);
  &:hover:not(:has(.ec-button-contentnode-add:hover, .resize-btn:hover)):not(
      :has(.content-node:hover)
    ) {
    border: 1px solid rgb(13, 71, 161, 0.6);
    background-color: rgb(13, 71, 161, 0.05);
  }
  border-radius: 4px;
}
.ec-layout-nodecard:not(.ec-layout-nodecard--layout-mode) :deep(.ec-content-nodecard) {
  background-color: white !important;
}
</style>
