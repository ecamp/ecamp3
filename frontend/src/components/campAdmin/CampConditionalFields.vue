<!--
Displays fields which don't apply to all camps, but are required for some
-->

<template>
  <v-expansion-panels class="mb-8 mt-2">
    <v-expansion-panel>
      <v-expansion-panel-header>
        <div class="subtitle-1 conditional-group-title">
          {{ $tc('components.campAdmin.campConditionalFields.ysCamp.title') }}
        </div>
      </v-expansion-panel-header>
      <v-expansion-panel-content>
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
      </v-expansion-panel-content>
    </v-expansion-panel>
    <v-expansion-panel>
      <v-expansion-panel-header>
        <div class="subtitle-1 conditional-group-title">
          {{ $tc('components.campAdmin.campConditionalFields.course.title') }}
        </div>
      </v-expansion-panel-header>
      <v-expansion-panel-content>
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
      </v-expansion-panel-content>
    </v-expansion-panel>
  </v-expansion-panels>
</template>

<script>
import ApiTextField from '@/components/form/api/ApiTextField.vue'
import ApiForm from '@/components/form/api/ApiForm.vue'
import ApiCheckbox from '../form/api/ApiCheckbox.vue'

export default {
  name: 'CampConditionalFields',
  components: { ApiTextField, ApiCheckbox, ApiForm },
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

<style scoped>
.conditional-group-title {
  font-size: 1rem !important;
}
</style>
