<template>
  <View class="content-node">
    <InstanceName :content-node="contentNode" :icon="mdiPen" />
    <View style="line-height: 0.8">
      <RichText :rich-text="contentNode.data.html" />
    </View>
  </View>
</template>
<script>
import PdfComponent from '@/pdf/PdfComponent.js'
import InstanceName from '../InstanceName.vue'
import { mdiPen } from '@mdi/js'
import RichText from '../../RichText.vue'
import { isEmptyHtml } from '../../helpers.js'

export default {
  name: 'Notes',
  components: { RichText, InstanceName },
  extends: PdfComponent,
  isEmpty(contentNode) {
    return isEmptyHtml(contentNode.data.html)
  },
  props: {
    contentNode: { type: Object, required: true },
  },
  data() {
    return { mdiPen }
  },
}
</script>
<style lang="react-pdf"></style>
