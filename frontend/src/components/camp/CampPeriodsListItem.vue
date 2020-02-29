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
          @click.stop="showEditDialog = true">
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
          @click.stop="showDeleteDialog = true">
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

    <edit-period-dialog v-model="showEditDialog" :period="period" />
    <delete-entity-dialog v-model="showDeleteDialog" :entity="period">
      the Period "{{ period.description }}"
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
      showEditDialog: false,
      showDeleteDialog: false
    }
  }
}
</script>

<style scoped>
</style>
