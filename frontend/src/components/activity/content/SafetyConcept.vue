<template>
  <card-content-node :content-node="contentNode">
    <div class="mb-3">
      <api-form :entity="contentNode.singleText()">
        <api-richtext
          fieldname="text"
          :placeholder="$tc('contentNode.safetyConcept.name')"
          rows="2" />
      </api-form>
    </div>
  </card-content-node>
</template>

<script>

import ApiForm from '@/components/form/api/ApiForm'
import ApiRichtext from '@/components/form/api/ApiRichtext'
import CardContentNode from '@/components/activity/CardContentNode'

export default {
  name: 'SafetyConcept',
  components: {
    CardContentNode,
    ApiForm,
    ApiRichtext
  },
  props: {
    contentNode: { type: Object, required: true }
  }
}
</script>

<style scoped>
</style>
