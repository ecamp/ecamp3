<template>
  <div class="mb-3">
    <api-form :entity="contentNode.singleText()">
      <api-richtext
        fieldname="text"
        :placeholder="$tc('contentNode.safetyConcept.name')"
        rows="2" />
    </api-form>
  </div>
</template>

<script>

import ApiForm from '@/components/form/api/ApiForm'
import ApiRichtext from '@/components/form/api/ApiRichtext'

export default {
  name: 'SafetyConcept',
  components: {
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
