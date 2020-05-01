<template>
  <div>
    {{ $options.name }} //
    I'm a TextArea content component: {{ eventPlugin.textarea().id }} {{ eventPlugin.textarea().text }}

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
