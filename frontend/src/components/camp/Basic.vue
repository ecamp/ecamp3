<!--
Displays details on a single camp and allows to edit them.
-->

<template>
  <v-card>
    <v-toolbar dense color="blue-grey lighten-5">
      <v-icon left>
        mdi-cogs
      </v-icon>
      <v-toolbar-title>
        Einstellungen
      </v-toolbar-title>
      <v-spacer />
    </v-toolbar>
    <v-skeleton-loader v-if="camp()._meta.loading" type="article" />
    <v-card-text v-else>
      <v-form>
        <v-text-field
          label="Name"
          readonly
          :value="camp().name"
          class="mr-2 ml-2" />
        <api-input
          :value="camp().title"
          :uri="camp()._meta.self"
          fieldname="title"
          label="Titel"
          required />
        <api-input
          :value="camp().motto"
          :uri="camp()._meta.self"
          fieldname="motto"
          label="Motto"
          required />
        <v-list>
          <v-label>Perioden</v-label>
          <v-list-item
            v-for="period in periods.items"
            :key="period.id">
            <v-list-item-content>
              <v-list-item-title>{{ period.description }}</v-list-item-title>
              <v-list-item-subtitle>{{ period.start }} - {{ period.end }}</v-list-item-subtitle>
            </v-list-item-content>
          </v-list-item>
        </v-list>
      </v-form>
    </v-card-text>
  </v-card>
</template>

<script>
import ApiInput from '../form/ApiInput'
export default {
  name: 'Basic',
  components: { ApiInput },
  props: {
    camp: { type: Function, required: true }
  },
  computed: {
    periods () {
      return this.camp().periods()
    }
  }
}
</script>

<style scoped>
  .v-list-item {
    padding-left: 0;
  }
</style>
