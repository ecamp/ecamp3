<template>
  <div class="e-pages-overview" :class="{ 'e-pages-overview--list-style': listStyle }">
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
    listStyle: Boolean,
    modelValue: {
      type: Array,
      default: () => [],
    },
  },
  emits: ['update:modelValue'],
}
</script>

<style scoped lang="scss">
.e-pages-overview {
  background: #eee;
  flex-grow: 1;
}
.e-pages-overview__grid {
  display: grid;
  gap: 1rem;
  padding: 1rem;
}

.e-pages-overview:not(.e-pages-overview--list-style) {
  position: relative;

  .e-pages-overview__grid {
    grid-template-columns: repeat(auto-fill, min(100%, var(--e-page-width)));
    justify-content: space-evenly;
    gap: 4rem var(--e-grid-gap);
    padding: var(--e-grid-padding);
  }
}
</style>
