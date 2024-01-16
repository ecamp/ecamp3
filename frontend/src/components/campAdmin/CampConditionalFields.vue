<!--
Displays fields which don't apply to all camps, but are required for some
-->

<template>
  <v-expansion-panel>
    <v-expansion-panel-title>
      <h2 class="text-subtitle-1 font-weight-bold">
        {{ $tc('components.campAdmin.campConditionalFields.title') }}
      </h2>
    </v-expansion-panel-title>
    <v-expansion-panel-text>
      <v-row class="pb-6">
        <v-col cols="12" md="6" class="pb-0">
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
        </v-col>
        <v-col cols="12" md="6" class="pb-0">
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
        </v-col>
      </v-row>
    </v-expansion-panel-text>
  </v-expansion-panel>
</template>

<script>
import ApiTextField from '@/components/form/api/ApiTextField.vue'
import ApiForm from '@/components/form/api/ApiForm.vue'
import ApiCheckbox from '../form/api/ApiCheckbox.vue'
import ContentGroup from '@/components/layout/ContentGroup.vue'

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
