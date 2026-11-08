<template>
  <div class="e-pages-overview">
    <draggable
      :model-value="modelValue"
      :item-key="() => undefined"
      handle=".handle"
      filter=".e-pages-config--template"
      class="e-pages-overview__grid"
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
    flex-grow: 1;
  }
  .e-pages-overview__grid {
    display: grid;
    grid-template-columns: repeat(auto-fill, min(100%, var(--e-page-width)));
    justify-content: space-evenly;
    gap: 4rem var(--e-grid-gap);
    padding: var(--e-grid-padding);
  }
}
</style>
