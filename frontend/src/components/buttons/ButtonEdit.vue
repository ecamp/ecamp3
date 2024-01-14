<template>
  <IconButton
    v-bind="$attrs"
    class="px-3 px-sm-4"
    color="primary"
    :icon="icon"
    :hide-label="hideLabel || $vuetify.display.xsOnly"
    v-on="$listeners"
  >
    <slot>{{ $tc('global.button.edit') }}</slot>
  </IconButton>
</template>

<script>
import IconButton from '@/components/buttons/IconButton.vue'

export default {
  name: 'ButtonEdit',
  components: { IconButton },
  props: {
    icon: { type: String, default: 'mdi-pencil' },
    hideLabel: { type: Boolean, default: false },
  },
}
</script>

<style scoped></style>
