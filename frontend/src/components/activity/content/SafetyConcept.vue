<template>
  <card-content-node v-bind="$props">
    <div class="mb-3">
      <api-form :entity="contentNode">
        <api-richtext
          fieldname="data.text"
          :placeholder="$tc('contentNode.safetyConcept.name')"
          rows="2"
          :disabled="layoutMode || disabled"
          :filled="layoutMode" />
      </api-form>
    </div>
  </card-content-node>
</template>

<script>

import ApiForm from '@/components/form/api/ApiForm.vue'
import ApiRichtext from '@/components/form/api/ApiRichtext.vue'
import CardContentNode from '@/components/activity/CardContentNode.vue'
import { contentNodeMixin } from '@/mixins/contentNodeMixin.js'

export default {
  name: 'SafetyConcept',
  components: {
    CardContentNode,
    ApiForm,
    ApiRichtext
  },
  mixins: [contentNodeMixin]
}
</script>

<style scoped>
</style>
