<template>
  <content-group :title="$tc('components.campAdmin.campMaterialLists.title')">
    <template #title-actions>
      <dialog-material-list-create v-if="!disabled" :camp="camp()">
        <template #activator="{ on }">
          <button-add
            color="secondary"
            text
            :hide-label="$vuetify.breakpoint.xsOnly"
            class="my-n1"
            v-on="on"
          >
            {{ $tc('components.campAdmin.campMaterialLists.createMaterialList') }}
          </button-add>
        </template>
      </dialog-material-list-create>
    </template>
    <v-skeleton-loader v-if="camp().materialLists()._meta.loading" type="article" />
    <v-list class="mx-n2">
      <dialog-material-list-edit
        v-for="materialList in materialLists.allItems"
        :key="materialList._meta.self"
        :material-list="materialList"
      >
        <template #activator="{ on }">
          <camp-material-lists-item
            class="px-0"
            :material-list="materialList"
            :disabled="disabled"
            v-on="on"
          />
        </template>
      </dialog-material-list-edit>
    </v-list>
  </content-group>
</template>

<script>
import ButtonAdd from '@/components/buttons/ButtonAdd.vue'
import CampMaterialListsItem from '@/components/campAdmin/CampMaterialListsItem.vue'
import DialogMaterialListCreate from './DialogMaterialListCreate.vue'
import ContentGroup from '@/components/layout/ContentGroup.vue'
import DialogMaterialListEdit from '@/components/campAdmin/DialogMaterialListEdit.vue'
import ButtonEdit from '@/components/buttons/ButtonEdit.vue'

export default {
  name: 'CampMaterialLists',
  components: {
    ButtonEdit,
    DialogMaterialListEdit,
    ContentGroup,
    ButtonAdd,
    CampMaterialListsItem,
    DialogMaterialListCreate,
  },
  props: {
    camp: { type: Function, required: true },
    disabled: { type: Boolean, default: false },
  },
  data() {
    return {}
  },
  computed: {
    materialLists() {
      return this.camp().materialLists()
    },
  },
}
</script>

<style scoped></style>
