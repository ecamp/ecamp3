<template>
  <View class="content-node">
    <InstanceName :content-node="contentNode" />
    <View style="line-height: 1.4">
      <RichText :rich-text="contentNode.data.html" />
    </View>
  </View>
</template>
<script>
import PdfComponent from '@/PdfComponent.js'
import InstanceName from '../InstanceName.vue'
import RichText from '../../RichText.vue'

export default {
  name: 'LearningObjectives',
  components: { RichText, InstanceName },
  extends: PdfComponent,
  props: {
    contentNode: { type: Object, required: true },
  },
}
</script>
<pdf-style>
</pdf-style>
