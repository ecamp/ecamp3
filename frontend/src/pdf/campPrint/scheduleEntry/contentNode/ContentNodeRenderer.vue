<template>
  <template v-if="!hidden">
    <component
      :is="contentNodeComponent"
      v-if="contentNodeComponent"
      :content-node="contentNode"
      :class="{ 'content-node-compact': compact && !isLayout }"
    />
    <Text v-else>{{ contentTypeName }}</Text>
  </template>
</template>
<script>
import PdfComponent from '@/pdf/PdfComponent.js'
import ColumnLayout from './ColumnLayout.vue'
import ResponsiveLayout from './ResponsiveLayout.vue'
import LAThematicArea from './LAThematicArea.vue'
import YSActivityCharacteristics from './YSActivityCharacteristics.vue'
import LearningObjectives from './LearningObjectives.vue'
import LearningTopics from './LearningTopics.vue'
import Storyboard from './Storyboard.vue'
import Notes from './Notes.vue'
import SafetyConsiderations from './SafetyConsiderations.vue'
import Material from './Material.vue'
import Storycontext from './Storycontext.vue'
import Checklist from './Checklist.vue'
import YSPhysicalActivityDuration from './YSPhysicalActivityDuration.vue'

export default {
  name: 'ContentNodeRenderer',
  extends: PdfComponent,
  inject: {
    camp: { default: null },
  },
  props: {
    contentNode: { type: Object, required: true },
  },
  computed: {
    contentTypeName() {
      return this.contentNode.contentTypeName
    },
    isLayout() {
      return ['ColumnLayout', 'ResponsiveLayout'].includes(this.contentTypeName)
    },
    contentNodeComponent() {
      return {
        ColumnLayout,
        ResponsiveLayout,
        LAThematicArea,
        YSActivityCharacteristics,
        LearningObjectives,
        LearningTopics,
        Storyboard,
        Notes,
        SafetyConsiderations,
        Material,
        Storycontext,
        Checklist,
        YSPhysicalActivityDuration,
      }[this.contentTypeName]
    },
    hidden() {
      if (!this.compact || !this.contentNodeComponent?.isEmpty) return false
      return this.contentNodeComponent.isEmpty(this.contentNode, this.camp)
    },
  },
}
</script>
<style lang="react-pdf">
.content-node {
  margin-bottom: 9pt;
}
.content-node-compact {
  margin-bottom: 6pt;
}
</style>
