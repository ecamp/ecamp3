<template>
  <div
    class="ec-layout-card"
    :class="{ 'ec-layout-card--layout-mode': layoutMode, 'ec-layout-card--root': isRoot }"
  >
    <div
      v-if="!isRoot && layoutMode"
      class="text-center d-flex align-center justify-center gap-1 ec-layout-card-header"
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
  border: 1px solid black;
  border-radius: 10px;
}

.ec-layout-card-header {
  border-bottom: 1px solid black;
  background: rgba(255, 255, 255, 0.42);
  border-top-left-radius: 9px;
  border-top-right-radius: 9px;
}
</style>
