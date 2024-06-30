<template>
  <SideBar :title="$tc('global.navigation.admin.title')" icon="mdi-menu">
    <v-list class="py-0">
      <SidebarListItem
        :to="adminRoute(camp, 'info')"
        :title="$tc('views.admin.sideBarAdmin.itemInfos')"
        icon="mdi-tent"
      />
      <SidebarListItem
        :to="adminRoute(camp, 'activity')"
        :title="$tc('views.admin.sideBarAdmin.itemActivity')"
        :subtitle="$tc('views.admin.sideBarAdmin.itemActivitySubtitle')"
        icon="mdi-view-dashboard-outline"
      />
      <SidebarListItem
        :to="adminRoute(camp, 'collaborators')"
        :title="$tc('views.admin.sideBarAdmin.itemCollaborators')"
        icon="mdi-account-group-outline"
      />
      <SidebarListItem
        :to="adminRoute(camp, 'checklists')"
        title="Checklists"
        icon="mdi-list-box-outline"
      />
      <SidebarListItem
        :to="adminRoute(camp, 'material')"
        :title="$tc('views.admin.sideBarAdmin.itemMaterialLists')"
        icon="mdi-package-variant-closed"
      />
      <SidebarListItem
        :to="adminRoute(camp, 'print')"
        :title="$tc('views.admin.sideBarAdmin.itemPrint')"
        icon="mdi-file-outline"
      />
    </v-list>
  </SideBar>
</template>

<script>
import SideBar from '@/components/navigation/SideBar.vue'
import { adminRoute, campRoute } from '@/router.js'
import { campRoleMixin } from '@/mixins/campRoleMixin.js'
import SidebarListItem from '@/components/layout/SidebarListItem.vue'

export default {
  name: 'SideBarAdmin',
  components: { SidebarListItem, SideBar },
  mixins: [campRoleMixin],
  props: {
    camp: {
      type: Object,
      required: true,
    },
  },
  methods: { adminRoute, campRoute },
}
</script>

<style scoped></style>
