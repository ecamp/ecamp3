<template>
  <PagesConfigListStyle
    v-if="listStyle"
    v-bind="$attrs"
    :multiple
    :landscape
    :button-variant
    :title
    @remove="$emit('remove')"
    @up="$emit('up')"
    @down="$emit('down')"
  >
    <slot />
  </PagesConfigListStyle>
  <PagesConfigPageStyle
    v-else
    v-bind="$attrs"
    :multiple
    :landscape
    :can-rotate
    :button-variant
    :title
    @remove="$emit('remove')"
    @rotate="$emit('rotate')"
  >
    <slot />
  </PagesConfigPageStyle>
</template>

<script>
import PagesConfigPageStyle from './PagesConfigPageStyle.vue'
import PagesConfigListStyle from './PagesConfigListStyle.vue'

export default {
  name: 'PagesConfig',
  components: { PagesConfigPageStyle, PagesConfigListStyle },
  inheritAttrs: false,
  props: {
    listStyle: Boolean,
    multiple: Boolean,
    landscape: Boolean,
    canRotate: Boolean,
    buttonVariant: Boolean,
    title: { type: String, required: true },
  },
  emits: ['remove', 'rotate', 'up', 'down'],
}
</script>

<style lang="scss" scoped>
:deep(.e-pages-config.e-pages-config--landscape) {
  --aspect: 297 / 210;
  flex-direction: column;
  align-items: flex-end;
}

:deep(.e-pages-config--button-variant) {
  opacity: 0.7;

  &:hover {
    opacity: 1;
  }
}

:deep(.e-pages-config__paper) {
  background: white;
  box-shadow:
    0 1px 3px rgba(0, 0, 0, 0.12),
    0 1px 2px rgba(0, 0, 0, 0.24);
  border-radius: 1px;
}
</style>
