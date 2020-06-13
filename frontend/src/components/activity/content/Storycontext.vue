<template>
  <div>
    <api-form :entity="activityContent.textarea()">
      <api-text-field
        fieldname="text"
        :label="$t('activity.content.Storycontext.name')" />
    </api-form>
  </div>
</template>

<script>

import ApiTextField from '@/components/form/api/ApiTextField'
import ApiForm from '@/components/form/api/ApiForm'

export default {
  name: 'Storycontext',
  components: {
    ApiForm,
    ApiTextField
  },
  props: {
    activityContent: { type: Object, required: true }
  }
}
</script>

<style scoped>
</style>
