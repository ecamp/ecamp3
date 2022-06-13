<template>
  <v-btn
    class="px-3 px-sm-4"
    min-width="0"
    :color="color"
    v-bind="$attrs"
    v-on="$listeners">
    <span :class="{ 'd-sr-only': hideLabel }">
      <slot>{{ $tc('global.button.add') }}</slot>
    </span>
    <v-icon :right="!hideLabel" size="150%">{{ icon }}</v-icon>
  </v-btn>
</template>

<script>
export default {
  name: 'ButtonAdd',
  props: {
    icon: { type: String, default: 'mdi-plus' },
    hideLabel: { type: Boolean, default: false },
    color: { type: String, default: 'success' }
  }
}
</script>

<style scoped></style>
