<template>
  <ContentNodeCard v-bind="$props">
    <api-richtext
      :uri="contentNode._meta.self"
      path="data.html"
      :placeholder="$tc('contentNode.storycontext.name')"
      rows="2"
      auto-grow
      :disabled="layoutMode || disabled"
      class="grow-v-slot"
    />
  </ContentNodeCard>
</template>

<script>
import ContentNodeCard from '@/components/activity/content/layout/ContentNodeCard.vue'
import { contentNodeMixin } from '@/mixins/contentNodeMixin.js'

export default {
  name: 'Storycontext',
  components: {
    ContentNodeCard,
  },
  mixins: [contentNodeMixin],
}
</script>
