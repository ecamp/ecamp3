<template>
  <div class="e-pages-overview">
    <draggable
      :model-value="modelValue"
      :item-key="() => undefined"
      handle=".handle"
      filter=".e-pages-config--template"
      class="e-pages-overview__grid pa-0 pa-md-8"
      v-bind="$attrs"
      @update:model-value="$emit('update:modelValue', $event)"
    >
      <template #item="slotProps">
        <div :key="slotProps.index">
          <slot name="item" v-bind="slotProps" />
        </div>
      </template>
      <template #footer>
        <slot />
      </template>
    </draggable>
  </div>
</template>

<script>
import Draggable from 'vuedraggable'

export default {
  name: 'PagesOverview',
  components: { Draggable },
  props: {
    modelValue: {
      type: Array,
      default: () => [],
    },
  },
  emits: ['update:modelValue'],
}
</script>

<style scoped lang="scss">
@use 'vuetify/settings';
@use 'sass:map';

@media #{map.get(settings.$display-breakpoints, 'md-and-up')} {
  .e-pages-overview {
    background: #eee;
    position: relative;
  }
  .e-pages-overview__grid {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(316px, 1fr));
    gap: 4rem 1rem;
  }
}
</style>
