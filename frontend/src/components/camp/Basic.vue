<!--
Displays details on a single camp and allows to edit them.
-->

<template>
  <div>
    <v-card class="mb-4">
      <v-toolbar dense color="blue-grey lighten-5">
        <v-icon left>
          mdi-cogs
        </v-icon>
        <v-toolbar-title>
          Einstellungen
        </v-toolbar-title>
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
        </v-form>
      </v-card-text>
    </v-card>
    <v-card class="mb-4">
      <v-toolbar dense color="blue-grey lighten-5">
        <v-icon left>
          mdi-calendar-plus
        </v-icon>
        <v-toolbar-title>
          Periods
        </v-toolbar-title>
      </v-toolbar>
      <v-skeleton-loader v-if="camp()._meta.loading" type="article" />
      <v-card-text v-else>
        <v-list>
          <v-list-item
            v-for="period in periods.items"
            :key="period.id">
            <v-list-item-content>
              <v-list-item-title>{{ period.description }}</v-list-item-title>
              <v-list-item-subtitle>{{ period.start }} - {{ period.end }}</v-list-item-subtitle>
            </v-list-item-content>
            <v-list-item-action style="display: inline">
              <v-item-group>
                <v-btn
                  small
                  color="primary"
                  class="mr-1"
                  @click="editPeriodUri=period._meta.self">
                  <span class="d-none d-sm-block">
                    <i class="v-icon v-icon--left mdi mdi-pencil" />
                    Edit
                  </span>
                  <span class="d-sm-none">
                    <i class="v-icon mdi mdi-pencil" />
                  </span>
                </v-btn>
                <v-btn
                  small
                  color="error"
                  @click="deletePeriod=period">
                  <span class="d-none d-sm-block">
                    <i class="v-icon v-icon--left mdi mdi-delete" />
                    Delete
                  </span>
                  <span class="d-sm-none">
                    <i class="v-icon mdi mdi-delete" />
                  </span>
                </v-btn>
              </v-item-group>
            </v-list-item-action>
          </v-list-item>
          <v-list-item>
            <v-list-item-content />
            <v-list-item-action>
              <v-btn
                small
                color="success"
                class="mb-1"
                @click="createPeriodCamp=camp()">
                <i class="v-icon v-icon--left mdi mdi-plus" />
                Create Period
              </v-btn>
            </v-list-item-action>
          </v-list-item>
        </v-list>
      </v-card-text>
    </v-card>

    <create-period-dialog v-model="createPeriodCamp" />
    <edit-period-dialog v-model="editPeriodUri" />
    <delete-entity-dialog v-model="deletePeriod">
      the Period "{{ (deletePeriod || {}).description }}"
    </delete-entity-dialog>
  </div>
</template>

<script>
import ApiInput from '../form/ApiInput'
import EditPeriodDialog from '../dialog/EditPeriodDialog'
import CreatePeriodDialog from '../dialog/CreatePeriodDialog'
import DeleteEntityDialog from '../dialog/DeleteEntityDialog'
export default {
  name: 'Basic',
  components: { ApiInput, DeleteEntityDialog, CreatePeriodDialog, EditPeriodDialog },
  props: {
    camp: { type: Function, required: true }
  },
  data () {
    return {
      editPeriodUri: '',
      createPeriodCamp: null,
      deletePeriod: null,
      messages: []
    }
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
