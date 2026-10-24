<template>
  <component
    :is="contentNodeComponent"
    v-if="contentNodeComponent"
    :content-node="contentNode"
  />
  <Text v-else>{{ contentTypeName }}</Text>
</template>
<script>
import PdfComponent from '@/pdf/PdfComponent.js'
import ColumnLayout from './ColumnLayout.vue'
import ResponsiveLayout from './ResponsiveLayout.vue'
import LAThematicArea from './LAThematicArea.vue'
import LearningObjectives from './LearningObjectives.vue'
import LearningTopics from './LearningTopics.vue'
import Storyboard from './Storyboard.vue'
import Notes from './Notes.vue'
import SafetyConsiderations from './SafetyConsiderations.vue'
import Material from './Material.vue'
import Storycontext from './Storycontext.vue'
import Checklist from './Checklist.vue'

export default {
  name: 'ContentNodeRenderer',
  extends: PdfComponent,
  props: {
    contentNode: { type: Object, required: true },
  },
  computed: {
    contentTypeName() {
      return this.contentNode.contentTypeName
    },
    contentNodeComponent() {
      return {
        ColumnLayout,
        ResponsiveLayout,
        LAThematicArea,
        LearningObjectives,
        LearningTopics,
        Storyboard,
        Notes,
        SafetyConsiderations,
        Material,
        Storycontext,
        Checklist,
      }[this.contentTypeName]
    },
  },
}
</script>
<style lang="react-pdf">
.content-node {
  margin-bottom: 6pt;
}
</style>
