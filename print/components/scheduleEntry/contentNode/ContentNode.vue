<!--
generic component to render any ContentNode
-->
<template>
  <div>
    <generic-error-message v-if="error" :error="error" />
    <component :is="componentFor(contentNode)" v-else :content-node="contentNode" />
  </div>
</template>

<script setup>
const props = defineProps({
  contentNode: { type: Object, required: true },
})

const { error } = await useAsyncData(
  `ContentNode-${props.contentNode._meta.self}`,
  async () => {
    await Promise.all([
      props.contentNode._meta.load,
      props.contentNode.children().$loadItems(),
      props.contentNode.contentType()._meta.load,
    ])
  }
)
</script>

<script>
import NotImplemented from './NotImplemented.vue'
import ColumnLayout from './ColumnLayout.vue'
import ResponsiveLayout from './ResponsiveLayout.vue'
import LAThematicArea from './LAThematicArea.vue'
import LearningObjectives from './LearningObjectives.vue'
import LearningTopics from './LearningTopics.vue'
import Material from './Material.vue'
import Notes from './Notes.vue'
import SafetyConsiderations from './SafetyConsiderations.vue'
import Storycontext from './Storycontext.vue'
import Storyboard from './Storyboard.vue'

export default defineNuxtComponent({
  components: {
    NotImplemented,
    ColumnLayout,
    ResponsiveLayout,
    LAThematicArea,
    LearningObjectives,
    LearningTopics,
    Material,
    Notes,
    SafetyConsiderations,
    Storyboard,
    Storycontext,
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
})
</script>
