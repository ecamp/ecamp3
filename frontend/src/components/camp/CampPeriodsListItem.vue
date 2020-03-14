<!--
Displays a single period as a list item including controls to edit and delete it.
-->

<template>
  <v-list-item>
    <v-list-item-content class="pt-0 pb-2">
      <v-list-item-title>{{ period.description }}</v-list-item-title>
      <v-list-item-subtitle>{{ period.start }} - {{ period.end }}</v-list-item-subtitle>
    </v-list-item-content>

    <v-list-item-action style="display: inline">
      <v-item-group>
        <dialog-period-edit :period="period">
          <template v-slot:activator="{ on }">
            <button-edit class="mr-1" v-on="on" />
          </template>
        </dialog-period-edit>

        <dialog-entity-delete :entity="period">
          <template v-slot:activator="{ on }">
            <button-delete v-on="on" />
          </template>
          the Period "{{ period.description }}"
        </dialog-entity-delete>
      </v-item-group>
    </v-list-item-action>
  </v-list-item>
</template>

<script>

import DialogPeriodEdit from '@/components/dialog/DialogPeriodEdit'
import DialogEntityDelete from '@/components/dialog/DialogEntityDelete'
import ButtonEdit from '@/components/base/ButtonEdit'
import ButtonDelete from '@/components/base/ButtonDelete'

export default {
  name: 'CampPeriods',
  components: { DialogEntityDelete, DialogPeriodEdit, ButtonEdit, ButtonDelete },
  props: {
    period: { type: Object, required: true }
  }
}
</script>

<style scoped>
</style>
