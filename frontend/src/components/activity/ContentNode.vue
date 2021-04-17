<template>
  <component :is="contentNode.contentTypeName"
             v-if="!contentNode.loading"
             :class="{ 'draggable-cursor': draggable && contentNode.parent !== null }"
             :content-node="contentNode"
             :layout-mode="layoutMode"
             :draggable="draggable"
             v-bind="$attrs" />
</template>

<script>

import ColumnLayout from './content/ColumnLayout.vue'
import Notes from './content/Notes.vue'
import Material from './content/Material.vue'
import LAThematicArea from './content/LAThematicArea.vue'
import SafetyConcept from './content/SafetyConcept.vue'
import Storycontext from './content/Storycontext.vue'
import Storyboard from './content/Storyboard.vue'

const contentNodeComponents = {
  ColumnLayout,
  Notes,
  Material,
  LAThematicArea,
  SafetyConcept,
  Storycontext,
  Storyboard,
}

export default {
  name: 'ContentNode',
  components: contentNodeComponents,
  props: {
    contentNode: { type: Object, required: true },
    layoutMode: { type: Boolean, required: true },
    draggable: { type: Boolean, default: false }
  }
}
</script>

<style lang="scss">
.draggable-cursor {
  cursor: pointer;

  &:hover {
    background: map-get($blue-grey, 'lighten-5');
  }

  .theme--light.v-toolbar.v-sheet {
    background: none;
  }
}

.draggable-cursor [disabled] {
  cursor: move;
}
</style>
