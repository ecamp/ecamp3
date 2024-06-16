<!--
Displays details on a single camp and allows to edit them.
-->

<template>
  <content-group
    :title="$tc('components.campAdmin.campSettings.title')"
    icon="mdi-information"
  >
    <v-skeleton-loader v-if="camp._meta.loading" type="article" />
    <div v-else class="mt-3">
      <api-form :entity="camp" name="camp">
        <api-text-field path="name" vee-rules="required" :disabled="disabled" />

        <api-text-field path="title" vee-rules="required" :disabled="disabled" />

        <api-text-field path="motto" :disabled="disabled" />
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
      type: Object,
      required: true,
    },
    disabled: {
      type: Boolean,
      default: false,
    },
  },
  data() {
    return {}
  },
}
</script>

<style scoped></style>
