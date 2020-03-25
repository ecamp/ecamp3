<!--
Displays details on a single camp and allows to edit them.
-->

<template>
  <content-group title="Einstellungen">
    <v-skeleton-loader v-if="camp()._meta.loading" type="article" />
    <v-form v-else>
      <v-text-field
        label="Name"
        outlined
        readonly
        hide-details="auto"
        :value="camp().name"
        class="my-4" />
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
    </v-form>
  </content-group>
</template>

<script>
import ApiTextField from '@/components/form/api/ApiTextField'
import ContentGroup from '@/components/layout/ContentGroup'
import ApiForm from '@/components/form/ApiForm'

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
