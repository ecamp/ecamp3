<template>
  <SideBar :title="$tc('global.navigation.admin.title')" icon="mdi-menu">
    <v-list class="py-0">
      <SidebarListItem
        :to="adminRoute(camp, 'info')"
        :title="$tc('views.camp.admin.sideBarAdmin.itemInfos')"
        icon="mdi-tent"
      />
      <SidebarListItem
        :to="adminRoute(camp, 'activity')"
        :title="$tc('views.camp.admin.sideBarAdmin.itemActivity')"
        :subtitle="$tc('views.camp.admin.sideBarAdmin.itemActivitySubtitle')"
        icon="mdi-view-dashboard-outline"
      />
      <SidebarListItem
        :to="adminRoute(camp, 'collaborators')"
        :title="$tc('views.camp.admin.sideBarAdmin.itemCollaborators')"
        icon="mdi-account-group-outline"
      />
      <SidebarListItem
        v-if="featureChecklistEnabled"
        :to="adminRoute(camp, 'checklists')"
        :title="$tc('entity.checklist.name', 2)"
        icon="mdi-clipboard-list-outline"
      />
      <SidebarListItem
        :to="adminRoute(camp, 'material')"
        :title="$tc('views.camp.admin.sideBarAdmin.itemMaterialLists')"
        icon="mdi-package-variant-closed"
      />
      <SidebarListItem
        :to="adminRoute(camp, 'print')"
        :title="$tc('views.camp.admin.sideBarAdmin.itemPrint')"
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
import { getEnv } from '@/environment.js'

export default {
  name: 'CampSideBarAdmin',
  components: { SidebarListItem, SideBar },
  mixins: [campRoleMixin],
  props: {
    camp: {
      type: Object,
      required: true,
    },
  },
  computed: {
    featureChecklistEnabled() {
      return getEnv().FEATURE_CHECKLIST ?? false
    },
  },
  methods: { adminRoute, campRoute },
}
</script>

<style scoped></style>
