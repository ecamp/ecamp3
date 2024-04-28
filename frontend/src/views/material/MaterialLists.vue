<!--
Show all material lists for a camp on mobile
-->

<template>
  <content-card :title="$tc('views.material.materialLists.title')" toolbar>
    <template v-if="!isGuest" #title-actions>
      <DialogMaterialListCreate :camp="camp">
        <template #activator="{ on }">
          <ButtonAdd class="mr-n2" height="32" v-on="on"
            >{{ $tc('global.button.create') }}
          </ButtonAdd>
        </template>
      </DialogMaterialListCreate>
    </template>
    <MaterialLists :camp="camp" />
  </content-card>
</template>

<script>
import ContentCard from '@/components/layout/ContentCard.vue'
import { campRoleMixin } from '@/mixins/campRoleMixin'
import ButtonAdd from '@/components/buttons/ButtonAdd.vue'
import DialogMaterialListCreate from '@/components/campAdmin/DialogMaterialListCreate.vue'
import MaterialLists from '@/components/material/MaterialLists.vue'

export default {
  name: 'MaterialLists',
  components: {
    MaterialLists,
    DialogMaterialListCreate,
    ButtonAdd,
    ContentCard,
  },
  mixins: [campRoleMixin],
  props: {
    camp: { type: Object, required: true },
  },
  computed: {
    materialLists() {
      return this.camp.materialLists()
    },
  },
  mounted() {
    this.materialLists.$loadItems()
  },
}
</script>

<style scoped></style>
