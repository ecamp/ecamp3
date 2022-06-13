<!--
Displays address and allows to edit
-->

<template>
  <content-group :title="$tc('components.camp.campAddress.title')">
    <v-skeleton-loader v-if="camp()._meta.loading" type="article" />
    <div v-else class="mt-3">
      <api-form :entity="camp()">
        <api-text-field
          fieldname="addressName"
          :name="$tc('entity.camp.fields.addressName')"
          :disabled="disabled" />
        <api-text-field
          fieldname="addressStreet"
          :name="$tc('entity.camp.fields.addressStreet')"
          :disabled="disabled" />
        <api-text-field
          fieldname="addressZipcode"
          :name="$tc('entity.camp.fields.addressZipcode')"
          :disabled="disabled" />
        <api-text-field
          fieldname="addressCity"
          :name="$tc('entity.camp.fields.addressCity')"
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
  name: 'CampAddress',
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
  }
}
</script>

<style scoped></style>
