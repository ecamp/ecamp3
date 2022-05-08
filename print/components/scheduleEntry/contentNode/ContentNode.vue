<template>
  <div>
    <component
      :is="componentFor(contentNode)"
      :content-node="contentNode"
    ></component>
  </div>
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
    await Promise.all([
      this.contentNode._meta.load,
      this.contentNode.children().$loadItems(),
      this.contentNode.contentType()._meta.load,
    ])
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
