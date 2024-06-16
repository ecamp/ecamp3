<!--
Displays fields which don't apply to all camps, but are required for some
-->

<template>
  <v-expansion-panel>
    <v-expansion-panel-header>
      <h2 class="subtitle-1 font-weight-bold d-flex align-center">
        <v-icon left size="20">{{
          $i18n.locale.includes('it') ? '$vuetify.icons.gs' : '$vuetify.icons.js'
        }}</v-icon>
        {{ $tc('components.campAdmin.campConditionalFields.title') }}
      </h2>
    </v-expansion-panel-header>
    <v-expansion-panel-content>
      <v-row class="pb-6">
        <v-col cols="12" md="6" class="pb-0">
          <content-group
            :title="$tc('components.campAdmin.campConditionalFields.ysCamp.title')"
            icon="mdi-tent"
          >
            <v-skeleton-loader v-if="camp._meta.loading" type="article" />
            <div v-else class="mt-3">
              <api-form :entity="camp" name="camp">
                <api-text-field path="organizer" :disabled="disabled" />

                <api-text-field path="kind" :disabled="disabled" />

                <api-text-field path="coachName" :disabled="disabled" />
              </api-form>
            </div>
          </content-group>
        </v-col>
        <v-col cols="12" md="6" class="pb-0">
          <content-group
            :title="$tc('components.campAdmin.campConditionalFields.course.title')"
            icon="mdi-school"
          >
            <v-skeleton-loader v-if="camp._meta.loading" type="article" />
            <div v-else class="mt-3">
              <api-form :entity="camp" name="camp">
                <api-text-field path="courseNumber" :disabled="disabled" />

                <api-text-field path="courseKind" :disabled="disabled" />

                <api-text-field path="trainingAdvisorName" :disabled="disabled" />

                <api-checkbox path="printYSLogoOnPicasso" :disabled="disabled" />
              </api-form>
            </div>
          </content-group>
        </v-col>
      </v-row>
    </v-expansion-panel-content>
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
      type: Object,
      required: true,
    },
    disabled: {
      type: Boolean,
      default: false,
    },
  },
}
</script>
