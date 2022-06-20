<template>
  <v-btn
    class="px-3 px-sm-4"
    min-width="0"
    :color="color"
    v-bind="$attrs"
    v-on="$listeners"
  >
    <span :class="{ 'd-sr-only': hideLabel }">
      <slot />
    </span>
    <v-icon :right="!hideLabel" size="150%" :class="{ animate: animate }">
      {{ icon }}
    </v-icon>
  </v-btn>
</template>

<script>
export default {
  name: 'IconButton',
  props: {
    icon: { type: String, required: true },
    hideLabel: { type: Boolean, default: false },
    color: { type: String, default: 'normal' },
    animate: { type: Boolean, default: false },
  },
}
</script>

<style scoped>
/*noinspection CssUnusedSymbol*/
.v-icon.animate {
  animation: spin 0.5s infinite;
}

@keyframes spin {
  from {
    transform: rotate(0deg);
  }
  to {
    transform: rotate(360deg);
  }
}
</style>
