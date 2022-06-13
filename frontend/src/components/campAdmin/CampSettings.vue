<!--
Displays details on a single camp and allows to edit them.
-->

<template>
  <content-group :title="$tc('components.camp.campSettings.title')">
    <v-skeleton-loader v-if="camp()._meta.loading" type="article" />
    <div v-else class="mt-3">
      <api-form :entity="camp()">
        <api-text-field
          fieldname="name"
          :name="$tc('entity.camp.fields.name')"
          vee-rules="required"
          :disabled="disabled" />
        <api-text-field
          fieldname="title"
          :name="$tc('entity.camp.fields.title')"
          vee-rules="required"
          :disabled="disabled" />
        <api-text-field
          fieldname="motto"
          :name="$tc('entity.camp.fields.motto')"
          :disabled="disabled" />
      </api-form>
    </div>
  </content-group>
</template>

<script>
import ApiTextField from '@/components/form/api/ApiTextField.vue'
import ContentGroup from '@/components/layout/ContentGroup.vue'
import ApiForm from '@/components/form/api/ApiForm.vue'

export default {
  name: 'CampSettings',
  components: { ContentGroup, ApiTextField, ApiForm },
  props: {
    camp: {
      type: Function,
      required: true
    },
    disabled: {
      type: Boolean,
      default: false
    }
  },
  data () {
    return {}
  }
}
</script>

<style scoped></style>
