<!--
Admin screen of a camp: Displays MaterialLists and MaterialItems
-->

<template>
  <content-card :title="$tc('views.camp.material.title')" toolbar>
    <template #title-actions>
      <DialogMaterialListCreate :camp="camp()">
        <template #activator="{ on }">
          <ButtonAdd class="mr-n2" height="32" v-on="on">{{
            $tc('global.button.create')
          }}</ButtonAdd>
        </template>
      </DialogMaterialListCreate>
    </template>
    <MaterialLists :camp="camp" />
  </content-card>
</template>

<script>
import ContentCard from '@/components/layout/ContentCard.vue'
import MaterialLists from '@/components/material/MaterialLists.vue'
import { campRoleMixin } from '@/mixins/campRoleMixin'
import ButtonAdd from '@/components/buttons/ButtonAdd.vue'
import DialogMaterialListCreate from '@/components/material/DialogMaterialListCreate.vue'

export default {
  name: 'Material',
  components: {
    DialogMaterialListCreate,
    ButtonAdd,
    MaterialLists,
    ContentCard,
  },
  mixins: [campRoleMixin],
  props: {
    camp: { type: Function, required: true },
  },
}
</script>

<style scoped></style>
