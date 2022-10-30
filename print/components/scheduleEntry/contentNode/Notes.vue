<template>
  <content-node-content :content-node="contentNode" :icon-path="mdiPen">
    <rich-text :rich-text="contentNode.data.html" />
  </content-node-content>
</template>

<script>
import RichText from '../../generic/RichText.vue'
import ContentNodeContent from './ContentNodeContent.vue'
import { mdiPen } from '@mdi/js'

export default {
  components: {
    RichText,
    ContentNodeContent,
  },
  props: {
    contentNode: { type: Object, required: true },
  },
  data() {
    return {
      mdiPen,
    }
  },
}
</script>
