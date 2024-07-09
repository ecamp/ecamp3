<!--
Displays address and allows to edit
-->

<template>
  <content-group
    :title="$tc('components.campAdmin.campAddress.title')"
    icon="mdi-map-marker"
  >
    <v-skeleton-loader v-if="camp._meta.loading" type="article" />
    <div v-else class="mt-3">
      <api-form :entity="camp" name="camp">
        <api-text-field path="addressName" :disabled="disabled" />

        <api-text-field path="addressStreet" :disabled="disabled" />

        <v-row class="mt-3">
          <v-col cols="12" md="4" class="pt-0">
            <api-text-field path="addressZipcode" :disabled="disabled" />
          </v-col>
          <v-col cols="12" md="8" class="pt-0 pl-md-0">
            <api-text-field path="addressCity" :disabled="disabled" />
          </v-col>
        </v-row>
      </api-form>
    </div>
  </content-group>
</template>

<script>
import ApiTextField from '@/components/form/api/ApiTextField.vue'
import ContentGroup from '@/components/layout/ContentGroup.vue'
import ApiForm from '@/components/form/api/ApiForm.vue'

export default {
  name: 'CampAddress',
  components: { ContentGroup, ApiTextField, ApiForm },
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

<style scoped></style>
