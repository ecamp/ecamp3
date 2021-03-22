<template>
  <card-content-node :content-node="contentNode" :layout-mode="layoutMode">
    <div class="mb-3">
      <api-form :entity="contentNode.singleText()">
        <api-textarea
          fieldname="text"
          :placeholder="$tc('contentNode.storycontext.name')"
          rows="2"
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
  name: 'Storycontext',
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
