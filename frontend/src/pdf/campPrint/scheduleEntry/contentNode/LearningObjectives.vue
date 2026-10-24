<template>
  <View class="content-node">
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

export default {
  name: 'LearningObjectives',
  components: { RichText, InstanceName },
  extends: PdfComponent,
  props: {
    contentNode: { type: Object, required: true },
  },
  data() {
    return { mdiSchool }
  },
}
</script>
<style lang="react-pdf"></style>
