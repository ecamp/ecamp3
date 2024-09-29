<template>
  <SideBar title="Admin" icon="mdi-menu">
    <v-list class="py-0">
      <SidebarListItem to="/admin/debug" title="Debug" icon="mdi-bug" />
      <SidebarListItem
        v-if="featureChecklistEnabled"
        to="/admin/checklists"
        :title="$tc('entity.checklist.name', 2)"
        icon="mdi-clipboard-list-outline"
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
