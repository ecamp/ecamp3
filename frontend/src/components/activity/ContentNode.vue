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
import ResponsiveLayout from './content/ResponsiveLayout.vue'
import Notes from './content/Notes.vue'
import Material from './content/Material.vue'
import LAThematicArea from './content/LAThematicArea.vue'
import LearningObjectives from './content/LearningObjectives.vue'
import LearningTopics from './content/LearningTopics.vue'
import SafetyConcept from './content/SafetyConcept.vue'
import Storyboard from './content/Storyboard.vue'
import Storycontext from './content/Storycontext.vue'

const contentNodeComponents = {
  ColumnLayout,
  ResponsiveLayout,
  Notes,
  Material,
  LAThematicArea,
  LearningObjectives,
  LearningTopics,
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
.draggable {
  cursor: move;
  transition: background-color 0.3s 25ms ease;

  .theme--light.v-toolbar.v-sheet {
    background: none;
  }
}
</style>
