<template>
  <div>
    <api-form :entity="eventPlugin.textarea()">
      <api-text-field
        fieldname="text"
        label="Text" />
    </api-form>
  </div>
</template>

<script>

import ApiTextField from '@/components/form/api/ApiTextField'
import ApiForm from '@/components/form/api/ApiForm'

export default {
  name: 'Textarea',
  components: {
    ApiForm,
    ApiTextField
  },
  props: {
    eventPlugin: { type: Object, required: true }
  }
}
</script>

<style scoped>
</style>
