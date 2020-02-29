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
        <edit-period-dialog :period="period">
          <template v-slot:activator="{ on }">
            <button-edit class="mr-1" v-on="on" />
          </template>
        </edit-period-dialog>

        <delete-entity-dialog v-model="showDeleteDialog" :entity="period">
          <template v-slot:activator="{ on }">
            <button-delete v-on="on" />
          </template>
          the Period "{{ period.description }}"
        </delete-entity-dialog>
      </v-item-group>
    </v-list-item-action>

    <delete-entity-dialog v-model="showDeleteDialog" :entity="period">
      the Period "{{ period.description }}"
    </delete-entity-dialog>
  </v-list-item>
</template>

<script>

import EditPeriodDialog from '@/components/dialog/EditPeriodDialog'
import DeleteEntityDialog from '@/components/dialog/DeleteEntityDialog'
import ButtonEdit from '@/components/base/ButtonEdit'
import ButtonDelete from '@/components/base/ButtonDelete'

export default {
  name: 'CampPeriods',
  components: { DeleteEntityDialog, EditPeriodDialog, ButtonEdit, ButtonDelete },
  props: {
    period: { type: Object, required: true }
  },
  data () {
    return {
      showDeleteDialog: false
    }
  }
}
</script>

<style scoped>
</style>
