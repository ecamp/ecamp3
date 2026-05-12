<template>
  <v-list-item
    :title="modelValue ? $t('global.button.lock') : $t('global.button.unlock')"
    :prepend-icon="modelValue ? 'mdi-lock' : 'mdi-lock-open-variant'"
    :disabled="disabled"
    v-bind="$attrs"
  />
</template>

<script>
export default {
  props: {
    modelValue: {
      type: Boolean,
      required: true,
    },
    disabled: {
      type: Boolean,
      required: false,
      default: false,
    },
  },
}
</script>
