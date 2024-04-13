<template>
  <ContentNodeCard v-bind="$props">
    <api-richtext
      :uri="contentNode._meta.self"
      path="data.html"
      :label="$tc('contentNode.notes.name')"
      rows="4"
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
  name: 'Notes',
  components: {
    ContentNodeCard,
  },
  mixins: [contentNodeMixin],
}
</script>
