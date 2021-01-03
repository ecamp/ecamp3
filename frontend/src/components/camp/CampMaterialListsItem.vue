<template>
  <v-list-item>
    <v-list-item-content class="pt-0 pb-2">
      <v-list-item-title>{{ materialList.name }}</v-list-item-title>
    </v-list-item-content>

    <v-list-item-action style="display: inline">
      <v-item-group>
        <dialog-material-list-edit :material-list="materialList">
          <template #activator="{ on }">
            <button-edit class="mr-1" v-on="on" />
          </template>
        </dialog-material-list-edit>
      </v-item-group>
    </v-list-item-action>

    <v-menu offset-y>
      <template #activator="{ on, attrs }">
        <v-btn icon v-bind="attrs" v-on="on">
          <v-icon>mdi-dots-vertical</v-icon>
        </v-btn>
      </template>
      <v-card>
        <v-item-group>
          <v-list-item-action>
            <dialog-entity-delete :entity="materialList">
              <template #activator="{ on }">
                <button-delete v-on="on" />
              </template>
              {{ $tc('components.camp.campMaterialListsItem.deleteWarning') }} <br>
              <ul>
                <li>
                  {{ materialList.name }}
                </li>
              </ul>
            </dialog-entity-delete>
          </v-list-item-action>
        </v-item-group>
      </v-card>
    </v-menu>
  </v-list-item>
</template>

<script>

import DialogMaterialListEdit from '@/components/dialog/DialogMaterialListEdit'
import DialogEntityDelete from '@/components/dialog/DialogEntityDelete'
import ButtonEdit from '@/components/buttons/ButtonEdit'
import ButtonDelete from '@/components/buttons/ButtonDelete'

export default {
  name: 'CampMaterialListsItem',
  components: { DialogEntityDelete, DialogMaterialListEdit, ButtonEdit, ButtonDelete },
  props: {
    materialList: { type: Object, required: true }
  }
}
</script>

<style scoped>
</style>
