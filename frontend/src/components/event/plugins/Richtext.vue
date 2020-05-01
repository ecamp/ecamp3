<template>
  <div>
    {{ $options.name }} //
    I'm a RichText content component (but Textarea in the backend): {{ eventPlugin.textarea().id }} {{ eventPlugin.textarea().text }}

    <api-form :entity="eventPlugin.textarea()">
      <api-text-field
        fieldname="text"
        label="Very rich text" />
    </api-form>
  </div>
</template>

<script>
import ApiTextField from '@/components/form/api/ApiTextField'
import ApiForm from '@/components/form/api/ApiForm'

export default {
  name: 'Richtext',
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
