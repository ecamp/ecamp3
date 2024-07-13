<template>
  <v-list>
    <v-skeleton-loader v-if="materialLists._meta.loading" type="list-item@3" />
    <DialogMaterialListEdit
      v-for="materialList in materialListsSorted"
      :key="materialList._meta.self"
      :material-list="materialList"
    >
      <template #activator="{ on }">
        <v-list-item exact-path v-on="on">
          <v-list-item-content>
            <v-list-item-title>{{ materialList.name }}</v-list-item-title>
          </v-list-item-content>
          <v-list-item-action class="e-collaborator-item__actions ml-2">
            <ButtonEdit color="primary--text" text class="my-n1 v-btn--has-bg" />
          </v-list-item-action>
        </v-list-item>
      </template>
    </DialogMaterialListEdit>
  </v-list>
</template>
<script>
import ButtonEdit from '@/components/buttons/ButtonEdit.vue'
import DialogMaterialListEdit from '@/components/campAdmin/DialogMaterialListEdit.vue'
import { sortBy } from 'lodash'

export default {
  name: 'MaterialListsEdit',
  components: { ButtonEdit, DialogMaterialListEdit },
  props: {
    materialLists: {
      type: Object,
      required: true,
    },
  },
  computed: {
    materialListsSorted() {
      return sortBy(
        this.materialLists.allItems,
        (list) =>
          (list.campCollaboration == null ? 'NonUserList_' : 'UserList_') +
          list.name.toLowerCase()
      )
    },
  },
}
</script>
