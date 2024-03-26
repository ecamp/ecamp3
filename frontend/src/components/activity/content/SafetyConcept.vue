<template>
  <ContentNodeCard v-bind="$props">
    <api-richtext
      :uri="contentNode._meta.self"
      path="data.html"
      :placeholder="$tc('contentNode.safetyConcept.name')"
      rows="2"
      :disabled="layoutMode || disabled"
      class="grow-v-slot"
    />
  </ContentNodeCard>
</template>

<script>
import ApiRichtext from '@/components/form/api/ApiRichtext.vue'
import ContentNodeCard from '@/components/activity/content/layout/ContentNodeCard.vue'
import { contentNodeMixin } from '@/mixins/contentNodeMixin.js'

export default {
  name: 'SafetyConcept',
  components: {
    ContentNodeCard,
    ApiRichtext,
  },
  mixins: [contentNodeMixin],
}
</script>

<style scoped></style>
