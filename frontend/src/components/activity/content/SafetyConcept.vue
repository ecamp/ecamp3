<template>
  <div class="mb-3">
    <api-form :entity="activityContent.singleText()">
      <api-richtext
        fieldname="text"
        :placeholder="$t('activityContent.safetyConcept.name')"
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
    activityContent: { type: Object, required: true }
  }
}
</script>

<style scoped>
</style>
