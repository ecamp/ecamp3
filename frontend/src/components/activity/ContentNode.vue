<template>
  <component
    :is="contentNode.contentTypeName"
    v-if="!contentNode.loading"
    class="content-node"
    :class="{ draggable: draggable && contentNode.parent !== null }"
    :content-node="contentNode"
    :layout-mode="layoutMode"
    :draggable="draggable"
    :disabled="disabled"
    v-bind="$attrs"
  />
</template>

<script>
import ColumnLayout from './content/ColumnLayout.vue'
import Notes from './content/Notes.vue'
import Material from './content/Material.vue'
import LAThematicArea from './content/LAThematicArea.vue'
import SafetyConcept from './content/SafetyConcept.vue'
import Storyboard from './content/Storyboard.vue'
import Storycontext from './content/Storycontext.vue'

const contentNodeComponents = {
  ColumnLayout,
  Notes,
  Material,
  LAThematicArea,
  SafetyConcept,
  Storyboard,
  Storycontext,
}

export default {
  name: 'ContentNode',
  components: contentNodeComponents,
  props: {
    contentNode: { type: Object, required: true },
    layoutMode: { type: Boolean, required: true },
    draggable: { type: Boolean, default: false },
    disabled: { type: Boolean, default: false },
  },
}
</script>

<style lang="scss">
.content-node:not(.draggable) + .content-node:not(.draggable) {
  border-top: 1px solid rgba(0, 0, 0, 0.12) !important;
  border-radius: 0 !important;
}

.draggable {
  cursor: move;

  &:hover {
    background: map-get($blue-grey, 'lighten-5');
  }

  .theme--light.v-toolbar.v-sheet {
    background: none;
  }
}
</style>
