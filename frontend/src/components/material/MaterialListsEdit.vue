<template>
  <v-list>
    <v-skeleton-loader
      v-if="materialLists._meta.loading"
      type="list-item@3"
      class="gap-4 pa-4"
    />
    <DialogMaterialListEdit
      v-for="materialList in materialListsSorted"
      :key="materialList._meta.self"
      :material-list="materialList"
    >
      <template #activator="{ props }">
        <v-list-item exact-path v-bind="props">
          <v-list-item-title class="py-3">{{ materialList.name }}</v-list-item-title>

          <template #append>
            <v-list-item-action class="e-collaborator-item__actions ml-2">
              <ButtonEdit color="primary" variant="tonal" class="my-n1" />
            </v-list-item-action>
          </template>
        </v-list-item>
      </template>
    </DialogMaterialListEdit>
  </v-list>
</template>
<script>
import ButtonEdit from '@/components/buttons/ButtonEdit.vue'
import DialogMaterialListEdit from '@/components/campAdmin/DialogMaterialListEdit.vue'
import materialListsSorted from '@/helpers/materialListsSorted.js'

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
      return materialListsSorted(this.materialLists.allItems)
    },
  },
}
</script>
