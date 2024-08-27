<template>
  <ContentNodeCard v-bind="$props">
    <api-richtext
      :uri="contentNode._meta.self"
      path="data.html"
      :placeholder="$tc('contentNode.safetyConsiderations.name')"
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
  name: 'SafetyConsiderations',
  components: {
    ContentNodeCard,
    ApiRichtext,
  },
  mixins: [contentNodeMixin],
}
</script>

<style scoped></style>
