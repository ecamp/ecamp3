<template>
  <content-node-content :content-node="contentNode" :icon-path="mdiSecurity">
    <rich-text :rich-text="contentNode.data.html" />
  </content-node-content>
</template>

<script>
import RichText from '../../generic/RichText.vue'
import ContentNodeContent from './ContentNodeContent.vue'
import { mdiSecurity } from '@mdi/js'

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
      mdiSecurity,
    }
  },
}
</script>
