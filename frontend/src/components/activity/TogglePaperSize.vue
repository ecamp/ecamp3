<template>
  <v-tooltip bottom>
    <template #activator="{ on }">
      <v-btn
        text
        icon
        class="ec-paper-size-toggle d-none d-md-block"
        :aria-label="
          value
            ? $tc('components.activity.togglePaperSize.switchToFullSize')
            : $tc('components.activity.togglePaperSize.switchToPaperSize')
        "
        v-bind="buttonProps"
        @click="$emit('input', !value)"
        v-on="on"
      >
        <v-icon v-if="value" class="resize-icon">$vuetify.icons.bigScreen </v-icon>
        <v-icon v-else class="resize-icon">$vuetify.icons.paperSize</v-icon>
      </v-btn>
    </template>
    {{
      value
        ? $tc('components.activity.togglePaperSize.switchToFullSize')
        : $tc('components.activity.togglePaperSize.switchToPaperSize')
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
