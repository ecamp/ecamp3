<template>
  <card-content-node v-bind="$props">
    <api-richtext
      :uri="contentNode._meta.self"
      fieldname="data.html"
      :label="$tc('contentNode.notes.name')"
      rows="4"
      auto-grow
      :disabled="layoutMode || disabled"
      :filled="layoutMode"
      height="100%"
    />
  </card-content-node>
</template>

<script>
import CardContentNode from '@/components/activity/CardContentNode.vue'
import { contentNodeMixin } from '@/mixins/contentNodeMixin.js'

export default {
  name: 'Notes',
  components: {
    CardContentNode,
  },
  mixins: [contentNodeMixin],
}
</script>
