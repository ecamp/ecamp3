<template>
  <div class="ec-layout-item">
    <slot />
  </div>
</template>

<script>
export default {
  name: 'LayoutItem',
  props: {
    basis: { type: [String, Number], required: true },
    grow: { type: [String, Number], required: true },
  },
}
</script>

<style scoped>
.ec-layout-item {
  display: flex;
  flex-direction: column;
  flex-basis: v-bind(basis);
  flex-grow: v-bind(grow);
  flex-shrink: 0;
}
</style>
