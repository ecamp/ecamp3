<template>
  <content-node-content :content-node="contentNode">
    <rich-text :rich-text="contentNode.data.text" />
  </content-node-content>
</template>

<script>
import RichText from '../../generic/RichText.vue'
import ContentNodeContent from './ContentNodeContent.vue'

export default {
  components: {
    RichText,
    ContentNodeContent,
  },
  props: {
    contentNode: { type: Object, required: true },
  },
}
</script>
