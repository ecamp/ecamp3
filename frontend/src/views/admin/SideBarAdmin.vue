<template>
  <SideBar title="Admin" icon="mdi-menu">
    <v-list class="py-0">
      <SidebarListItem to="/admin/debug" title="Debug" icon="mdi-bug" />
      <SidebarListItem
        to="/admin/checklists"
        :title="$t('entity.checklist.name', 2)"
        icon="mdi-clipboard-list-outline"
      />
    </v-list>
  </SideBar>
</template>

<script>
import SideBar from '@/components/navigation/SideBar.vue'
import SidebarListItem from '@/components/layout/SidebarListItem.vue'

export default {
  name: 'SideBarAdmin',
  components: { SidebarListItem, SideBar },
}
</script>

<style scoped></style>
