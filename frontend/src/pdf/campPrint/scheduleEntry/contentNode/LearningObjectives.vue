<template>
  <View class="content-node" :class="{ 'content-node-compact': compact }">
    <InstanceName :content-node="contentNode" :icon="mdiSchool" />
    <View style="line-height: 0.8">
      <RichText :rich-text="contentNode.data.html" />
    </View>
  </View>
</template>
<script>
import PdfComponent from '@/pdf/PdfComponent.js'
import InstanceName from '../InstanceName.vue'
import { mdiSchool } from '@mdi/js'
import RichText from '../../RichText.vue'
import { isEmptyHtml } from '../../helpers.js'

export default {
  name: 'LearningObjectives',
  components: { RichText, InstanceName },
  extends: PdfComponent,
  isEmpty(contentNode) {
    return isEmptyHtml(contentNode.data.html)
  },
  props: {
    contentNode: { type: Object, required: true },
  },
  data() {
    return { mdiSchool }
  },
}
</script>
<style lang="react-pdf"></style>
