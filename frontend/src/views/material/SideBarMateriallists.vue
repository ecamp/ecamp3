<template>
  <side-bar>
    <content-card>
      <v-list-item
        :to="campRoute(camp(), 'material')"
        exact
        color="secondary"
        class="text-uppercase subtitle-2"
      >
        {{ $tc('views.material.sideBarMateriallists.title') }}
      </v-list-item>
      <MaterialLists :camp="camp" />
    </content-card>
  </side-bar>
</template>

<script>
import SideBar from '@/components/navigation/SideBar.vue'
import ContentCard from '@/components/layout/ContentCard.vue'
import { campRoute } from '@/router.js'
import MaterialLists from '@/components/material/MaterialLists.vue'

export default {
  name: 'SideBarMateriallists',
  components: { MaterialLists, ContentCard, SideBar },
  props: {
    camp: { type: Function, required: true },
  },
  methods: {
    campRoute,
  },
}
</script>
