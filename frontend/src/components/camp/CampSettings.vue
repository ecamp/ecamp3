<!--
Displays details on a single camp and allows to edit them.
-->

<template>
  <content-card icon="mdi-cogs" title="Einstellungen">
    <v-skeleton-loader v-if="camp()._meta.loading" type="article" />
    <v-form v-else>
      <v-text-field
        label="Name"
        readonly
        :value="camp().name"
        class="mr-2 ml-2" />
      <api-text-field
        :value="camp().title"
        :uri="camp()._meta.self"
        fieldname="title"
        label="Titel"
        required />
      <api-text-field
        :value="camp().motto"
        :uri="camp()._meta.self"
        fieldname="motto"
        label="Motto"
        required />
    </v-form>
  </content-card>
</template>

<script>
import ContentCard from '@/components/base/ContentCard'
import ApiTextField from '@/components/form/ApiTextField'

export default {
  name: 'CampSettings',
  components: { ApiTextField, ContentCard },
  props: {
    camp: { type: Function, required: true }
  },
  data () {
    return {
    }
  }
}
</script>

<style scoped>
</style>
