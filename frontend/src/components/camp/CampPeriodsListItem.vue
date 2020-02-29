<!--
Displays details on a single camp and allows to edit them.
-->

<template>
  <v-list-item>
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

    <edit-period-dialog v-model="editPeriodUri" />
    <delete-entity-dialog v-model="deletePeriod">
      the Period "{{ (deletePeriod || {}).description }}"
    </delete-entity-dialog>
  </v-list-item>
</template>

<script>

import EditPeriodDialog from '@/components/dialog/EditPeriodDialog'
import DeleteEntityDialog from '@/components/dialog/DeleteEntityDialog'

export default {
  name: 'CampPeriods',
  components: { DeleteEntityDialog, EditPeriodDialog },
  props: {
    period: { type: Object, required: true }
  },
  data () {
    return {
      editPeriodUri: '',
      deletePeriod: null
    }
  }
}
</script>

<style scoped>
</style>
