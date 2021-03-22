<template>
  <card-content-node :content-node="contentNode" :layout-mode="layoutMode">
    <div class="mb-3">
      <api-form :entity="contentNode.singleText()">
        <api-richtext
          fieldname="text"
          :placeholder="$tc('contentNode.safetyConcept.name')"
          rows="2"
          :disabled="layoutMode"
          :filled="layoutMode" />
      </api-form>
    </div>
  </card-content-node>
</template>

<script>

import ApiForm from '@/components/form/api/ApiForm'
import ApiRichtext from '@/components/form/api/ApiRichtext'
import CardContentNode from '@/components/activity/CardContentNode'
import { contentNodeMixin } from '@/mixins/contentNodeMixin'

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
