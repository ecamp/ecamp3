<!--
Displays fields which don't apply to all camps, but are required for some
-->

<template>
  <div>
    <content-group
      :title="$tc('components.campAdmin.campConditionalFields.ysCamp.title')"
    >
      <v-skeleton-loader v-if="camp()._meta.loading" type="article" />
      <div v-else class="mt-3">
        <api-form :entity="camp()">
          <api-text-field
            fieldname="organizer"
            :name="$tc('entity.camp.fields.organizer')"
            :disabled="disabled"
          />
          <api-text-field
            fieldname="kind"
            :name="$tc('entity.camp.fields.kind')"
            :disabled="disabled"
          />
          <api-text-field
            fieldname="coachName"
            :name="$tc('entity.camp.fields.coachName')"
            :disabled="disabled"
          />
        </api-form>
      </div>
    </content-group>
    <content-group
      :title="$tc('components.campAdmin.campConditionalFields.course.title')"
    >
      <v-skeleton-loader v-if="camp()._meta.loading" type="article" />
      <div v-else class="mt-3">
        <api-form :entity="camp()">
          <api-text-field
            fieldname="courseNumber"
            :name="$tc('entity.camp.fields.courseNumber')"
            :disabled="disabled"
          />
          <api-text-field
            fieldname="courseKind"
            :name="$tc('entity.camp.fields.courseKind')"
            :disabled="disabled"
          />
          <api-text-field
            fieldname="trainingAdvisorName"
            :name="$tc('entity.camp.fields.trainingAdvisorName')"
            :disabled="disabled"
          />
          <api-checkbox
            fieldname="printYSLogoOnPicasso"
            :name="$tc('entity.camp.fields.printYSLogoOnPicasso')"
            :disabled="disabled"
          />
        </api-form>
      </div>
    </content-group>
  </div>
</template>

<script>
import ApiTextField from '@/components/form/api/ApiTextField.vue'
import ContentGroup from '@/components/layout/ContentGroup.vue'
import ApiForm from '@/components/form/api/ApiForm.vue'
import ApiCheckbox from '../form/api/ApiCheckbox.vue'

export default {
  name: 'CampConditionalFields',
  components: { ContentGroup, ApiTextField, ApiCheckbox, ApiForm },
  props: {
    camp: {
      type: Function,
      required: true,
    },
    disabled: {
      type: Boolean,
      default: false,
    },
  },
}
</script>

<style scoped></style>
