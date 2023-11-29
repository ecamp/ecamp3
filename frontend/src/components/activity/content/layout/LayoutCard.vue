<template>
  <div
    class="ec-layout-card"
    :class="{
      'ec-layout-card--layout-mode elevation-4': layoutMode,
      'ec-layout-card--root': isRoot,
    }"
  >
    <div
      v-if="!isRoot && layoutMode"
      class="text-center d-flex align-center px-2 justify-space-between gap-1 ec-layout-card-header"
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
.ec-layout-card--layout-mode:not(.ec-layout-card--root) {
  border: 1px solid rgb(0 0 0 / 28%);
  border-radius: 4px;
}
</style>
