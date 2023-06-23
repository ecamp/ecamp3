<template>
  <View class="content-node">
    <InstanceName :content-node="contentNode" />
    <RichText :rich-text="contentNode.data.html" />
  </View>
</template>
<script>
import PdfComponent from '@/PdfComponent.js'
import InstanceName from '../InstanceName.vue'
import RichText from '../../RichText.vue'

export default {
  name: 'Notes',
  components: { RichText, InstanceName },
  extends: PdfComponent,
  props: {
    contentNode: { type: Object, required: true },
  },
}
</script>
<pdf-style>
</pdf-style>
