<template>
  <card-content-node v-bind="$props">
    <api-richtext
      :uri="contentNode._meta.self"
      fieldname="data.html"
      :placeholder="$tc('contentNode.safetyConcept.name')"
      rows="2"
      :disabled="layoutMode || disabled"
      :readonly="layoutMode"
      class="grow-v-slot"
    />
  </card-content-node>
</template>

<script>
import ApiRichtext from '@/components/form/api/ApiRichtext.vue'
import CardContentNode from '@/components/activity/CardContentNode.vue'
import { contentNodeMixin } from '@/mixins/contentNodeMixin.js'

export default {
  name: 'SafetyConcept',
  components: {
    CardContentNode,
    ApiRichtext,
  },
  mixins: [contentNodeMixin],
}
</script>

<style scoped></style>
