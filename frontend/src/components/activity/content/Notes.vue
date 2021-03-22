<template>
  <card-content-node :content-node="contentNode" :layout-mode="layoutMode">
    <div class="mb-3">
      <api-form :entity="contentNode.singleText()">
        <api-textarea
          fieldname="text"
          :label="$tc('contentNode.notes.name')"
          rows="4"
          auto-grow
          :disabled="layoutMode"
          :filled="layoutMode" />
      </api-form>
    </div>
  </card-content-node>
</template>

<script>

import ApiTextarea from '@/components/form/api/ApiTextarea'
import ApiForm from '@/components/form/api/ApiForm'
import CardContentNode from '@/components/activity/CardContentNode'
import { contentNodeMixin } from '@/mixins/contentNodeMixin'

export default {
  name: 'Notes',
  components: {
    CardContentNode,
    ApiForm,
    ApiTextarea
  },
  mixins: [contentNodeMixin]
}
</script>

<style scoped>
</style>
