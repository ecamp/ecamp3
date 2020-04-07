<!--
Displays details on a single camp and allows to edit them.
-->

<template>
  <content-group title="Einstellungen">
    <v-skeleton-loader v-if="camp()._meta.loading" type="article" />
    <div v-else>
      <e-text-field
        label="Name"
        readonly
        hide-details="auto"
        :value="camp().name" />
      <api-form :entity="camp()">
        <api-text-field
          fieldname="title"
          label="Titel"
          required />
        <api-text-field
          fieldname="motto"
          label="Motto"
          required />
      </api-form>
    </div>
  </content-group>
</template>

<script>
import ApiTextField from '@/components/form/api/ApiTextField'
import ContentGroup from '@/components/layout/ContentGroup'
import ApiForm from '@/components/form/api/ApiForm'

export default {
  name: 'CampSettings',
  components: { ContentGroup, ApiTextField, ApiForm },
  props: {
    camp: {
      type: Function,
      required: true
    }
  },
  data () {
    return {}
  }
}
</script>

<style scoped>
</style>
