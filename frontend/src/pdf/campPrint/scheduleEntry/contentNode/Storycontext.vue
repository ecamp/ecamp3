<template>
  <View class="content-node">
    <InstanceName :content-node="contentNode" />
    <View style="line-height: 0.8">
      <RichText :rich-text="contentNode.data.html" />
    </View>
  </View>
</template>
<script>
import PdfComponent from '@/pdf/PdfComponent.js'
import InstanceName from '../InstanceName.vue'
import RichText from '../../RichText.vue'

export default {
  name: 'Storycontext',
  components: { RichText, InstanceName },
  extends: PdfComponent,
  props: {
    contentNode: { type: Object, required: true },
  },
}
</script>
<style lang="react-pdf"></style>
