<template>
  <div>
    <div
      v-if="!isRoot && layoutMode"
      class="text-center d-flex align-center justify-center gap-1 ec-content-layout-header"
    >
      <slot name="header" />
    </div>
    <slot />
  </div>
</template>

<script>
export default {
  name: 'ContentLayout',
  props: {
    layoutMode: { type: Boolean, default: false },
    isRoot: { type: Boolean, default: false },
  },
}
</script>

<style scoped>
.ec-content-layout-header {
  border-bottom: 1px solid black;
  background: rgba(255, 255, 255, 0.42);
  border-top-left-radius: 9px;
  border-top-right-radius: 9px;
}
</style>
