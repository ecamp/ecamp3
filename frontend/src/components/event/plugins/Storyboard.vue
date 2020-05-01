<template>
  <div>
    {{ $options.name }} //
    I'm a Storboard content component:
    <div v-for="section in eventPlugin.sections().items" :key="section._meta.self">
      {{ section.id }} {{ section.text }}

      <api-form :entity="section">
        <api-text-field
          fieldname="text"
          label="Storyboard Text" />
      </api-form>
    </div>
  </div>
</template>

<script>
import ApiTextField from '@/components/form/api/ApiTextField'
import ApiForm from '@/components/form/api/ApiForm'

export default {
  name: 'Storyboard',
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
