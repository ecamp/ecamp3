<!--
Show all material lists for a camp on mobile
-->

<template>
  <content-card :title="$tc('views.camp.admin.adminMaterialLists.title')" toolbar>
    <template v-if="!isGuest" #title-actions>
      <DialogMaterialListCreate :camp="camp">
        <template #activator="{ on }">
          <ButtonAdd class="mr-n2" height="32" v-on="on"
            >{{ $tc('global.button.create') }}
          </ButtonAdd>
        </template>
      </DialogMaterialListCreate>
    </template>
    <MaterialListsEdit v-if="!isGuest" :material-lists="materialLists" />
    <MaterialLists v-else :camp="camp" />
  </content-card>
</template>

<script>
import ContentCard from '@/components/layout/ContentCard.vue'
import { campRoleMixin } from '@/mixins/campRoleMixin'
import ButtonAdd from '@/components/buttons/ButtonAdd.vue'
import DialogMaterialListCreate from '@/components/campAdmin/DialogMaterialListCreate.vue'
import { materialListRoute } from '@/router.js'
import MaterialLists from '@/components/material/MaterialLists.vue'
import MaterialListsEdit from '@/components/material/MaterialListsEdit.vue'

export default {
  name: 'CampAdminMaterialLists',
  components: {
    MaterialListsEdit,
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
  methods: { materialListRoute },
}
</script>

<style scoped></style>
