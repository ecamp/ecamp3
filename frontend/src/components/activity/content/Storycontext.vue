<template>
  <card-content-node v-bind="$props">
    <api-richtext
      :uri="contentNode._meta.self"
      fieldname="data.html"
      :placeholder="$tc('contentNode.storycontext.name')"
      rows="2"
      auto-grow
      :disabled="layoutMode || disabled"
      :readonly="layoutMode"
      class="grow-v-slot"
    />
  </card-content-node>
</template>

<script>
import CardContentNode from '@/components/activity/CardContentNode.vue'
import { contentNodeMixin } from '@/mixins/contentNodeMixin.js'

export default {
  name: 'Storycontext',
  components: {
    CardContentNode,
  },
  mixins: [contentNodeMixin],
}
</script>
