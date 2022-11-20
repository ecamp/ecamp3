<template>
  <div class="e-pages-overview">
    <draggable
      handle=".handle"
      filter=".e-pages-config--template"
      class="e-pages-overview__grid pa-0 pa-md-8"
    >
      <slot />
    </draggable>
    <slot name="drawer" />
  </div>
</template>

<script>
import Draggable from 'vuedraggable'

export default {
  name: 'PagesOverview',
  components: { Draggable },
}
</script>

<style scoped lang="scss">
@media #{map-get($display-breakpoints, 'md-and-up')} {
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
