<!--
Displays details on a single camp and allows to edit them.
-->

<template>
  <content-group :title="$tc('components.camp.campSettings.title')">
    <v-skeleton-loader v-if="camp()._meta.loading" type="article" />
    <div v-else>
      <e-text-field
        class="mt-2"
        :label="$tc('entity.camp.fields.name')"
        readonly
        :value="camp().name" />
      <api-form :entity="camp()">
        <api-text-field
          fieldname="title"
          :name="$tc('entity.camp.fields.title')"
          vee-rules="required" />
        <api-text-field
          fieldname="motto"
          :name="$tc('entity.camp.fields.motto')"
          vee-rules="required" />
      </api-form>
    </div>
  </content-group>
</template>

<script>
import ApiTextField from '@/components/form/api/ApiTextField'
import ContentGroup from '@/components/layout/ContentGroup'
import ApiForm from '@/components/form/api/ApiForm'
import ETextField from '@/components/form/base/ETextField'

export default {
  name: 'CampSettings',
  components: { ETextField, ContentGroup, ApiTextField, ApiForm },
  props: {
    camp: {
      type: Function,
      required: true
    }
  },
  data () {
    return {
      promptText: ''
    }
  }
}
</script>

<style scoped>
</style>
