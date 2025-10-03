<template>
  <v-tooltip bottom>
    <template #activator="{ props }">
      <v-btn
        text
        icon
        class="ec-paper-size-toggle d-none d-md-block"
        :aria-label="
          value
            ? $t('components.activity.togglePaperSize.switchToFullSize')
            : $t('components.activity.togglePaperSize.switchToPaperSize')
        "
        @click="$emit('input', !value)"
        v-bind="{ ...buttonProps, ...props }"
      >
        <v-icon v-if="value" class="resize-icon" icon="$bigScreen" />
        <v-icon v-else class="resize-icon" icon="$paperSize" />
      </v-btn>
    </template>
    {{
      value
        ? $t('components.activity.togglePaperSize.switchToFullSize')
        : $t('components.activity.togglePaperSize.switchToPaperSize')
    }}
  </v-tooltip>
</template>

<script>
export default {
  name: 'TogglePaperSize',
  props: {
    value: { type: Boolean, default: false },
    buttonProps: { type: Object, default: () => ({}) },
  },
}
</script>

<style scoped lang="scss">
.resize-icon,
.resize-icon :deep(svg) {
  width: 28px !important;
}
</style>
