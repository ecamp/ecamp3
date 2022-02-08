<template>
  <component
    class="content-node"
    :is="componentFor(contentNode)"
    :content-node="contentNode"
  ></component>
</template>

<script>
import NotImplemented from './NotImplemented.vue'
import ColumnLayout from './ColumnLayout.vue'
import LAThematicArea from './LAThematicArea.vue'
import Material from './Material.vue'
import Notes from './Notes.vue'
import SafetyConcept from './SafetyConcept.vue'
import Storycontext from './Storycontext.vue'
import Storyboard from './Storyboard.vue'

export default {
  components: {
    NotImplemented,
    ColumnLayout,
    LAThematicArea,
    Material,
    Notes,
    SafetyConcept,
    Storyboard,
    Storycontext,
  },
  props: {
    contentNode: { type: Object, required: true },
  },
  async fetch() {
    await this.contentNode._meta.load
  },
  methods: {
    componentFor(contentNode) {
      const contentTypeName = contentNode.contentType().name
      if (contentTypeName in this.$options.components) {
        return contentTypeName
      }
      return 'NotImplemented'
    },
  },
}
</script>

<style>
  .content-node + .content-node {
    margin-top: 20px;
  }
</style>
