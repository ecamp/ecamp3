<template>
  <View class="content-node" :class="{ 'content-node-compact': compact }">
    <InstanceName :content-node="contentNode" :icon="mdiSecurity" />
    <View style="line-height: 0.8">
      <RichText :rich-text="contentNode.data.html" />
    </View>
  </View>
</template>
<script>
import PdfComponent from '@/pdf/PdfComponent.js'
import InstanceName from '../InstanceName.vue'
import { mdiSecurity } from '@mdi/js'
import RichText from '../../RichText.vue'
import { isEmptyHtml } from '../../helpers.js'

export default {
  name: 'SafetyConsiderations',
  components: { RichText, InstanceName },
  extends: PdfComponent,
  isEmpty(contentNode) {
    return isEmptyHtml(contentNode.data.html)
  },
  props: {
    contentNode: { type: Object, required: true },
  },
  data() {
    return { mdiSecurity }
  },
}
</script>
<style lang="react-pdf"></style>
