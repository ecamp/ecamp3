<template>
  <dialog-material-list-edit :material-list="materialList">
    <template #activator="{ on }">
      <v-hover v-slot="{ hover }">
        <v-list-item v-if="!materialList._meta.loading" class="px-sm-0"
                     v-on="on">
          <v-list-item-content>
            <v-list-item-title>{{ materialList.name }}</v-list-item-title>
          </v-list-item-content>
          <v-list-item-icon>
            <v-icon v-show="hover" small>mdi-pencil</v-icon>
          </v-list-item-icon>
        </v-list-item>
      </v-hover>
    </template>
  </dialog-material-list-edit>
</template>

<script>
import DialogMaterialListEdit from './DialogMaterialListEdit.vue'

export default {
  name: 'CampMaterialListsItem',
  components: {
    DialogMaterialListEdit
  },
  props: {
    materialList: {
      type: Object,
      required: true
    },
    disabled: {
      type: Boolean,
      default: false
    }
  }
}
</script>

<style scoped>
</style>
