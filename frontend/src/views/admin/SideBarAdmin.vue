<template>
  <SideBar title="Admin" icon="mdi-menu">
    <v-list class="py-0">
      <SidebarListItem to="/admin/debug" title="Debug" />
      <SidebarListItem
        v-if="featureChecklistEnabled"
        to="/admin/checklists"
        title="Checklists"
      />
    </v-list>
  </SideBar>
</template>

<script>
import SideBar from '@/components/navigation/SideBar.vue'
import SidebarListItem from '@/components/layout/SidebarListItem.vue'
import { getEnv } from '@/environment.js'

export default {
  name: 'SideBarAdmin',
  components: { SidebarListItem, SideBar },
  computed: {
    featureChecklistEnabled() {
      return getEnv().FEATURE_CHECKLIST ?? false
    },
  },
}
</script>

<style scoped></style>
