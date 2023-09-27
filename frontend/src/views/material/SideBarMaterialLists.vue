<template>
  <side-bar>
    <content-card>
      <v-list-item exact color="secondary" class="text-uppercase subtitle-2">
        {{ $tc('views.material.sideBarMaterialLists.title') }}
      </v-list-item>
      <MaterialLists :camp="camp" />
    </content-card>
  </side-bar>
</template>

<script>
import SideBar from '@/components/navigation/SideBar.vue'
import ContentCard from '@/components/layout/ContentCard.vue'
import MaterialLists from '@/components/material/MaterialLists.vue'
export default {
  name: 'SideBarMaterialLists',
  components: { MaterialLists, ContentCard, SideBar },
  props: {
    camp: { type: Function, required: true },
  },
}
</script>
