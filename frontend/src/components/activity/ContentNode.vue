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

let contentNodeComponents = {}
/*
// Webpack is running (require.context needs to be available)
const context = require.context('@/components/activity/content', false)
contentNodeComponents = Object.fromEntries(context.keys().map(contentNodeComponent => {
  const component = context(contentNodeComponent).default
  return [component.name, component]
}))

// Vite is running (import.meta needs be available)
  const context = import.meta.globEager('/src/components/activity/content/*')
  contentNodeComponents = Object.fromEntries(Object.keys(context).map(contentNodeComponent => {
    const component = context[contentNodeComponent].default
    return [component.name, component]
  }))
}
*/

// manual import
contentNodeComponents = {
  ColumnLayout,
  Notes,
  Material,
  LAThematicArea,
  SafetyConcept,
  Storycontext
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
