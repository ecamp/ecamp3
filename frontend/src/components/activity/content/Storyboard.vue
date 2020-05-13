<template>
  <div>
    <div v-for="section in activityContent.sections().items" :key="section._meta.self">
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
    activityContent: { type: Object, required: true }
  }
}
</script>

<style scoped>
</style>
