<template>
  <SideBar
    :title="$tc('views.camp.material.sideBarMaterialLists.title')"
    icon="mdi-format-list-checkbox"
  >
    <MaterialLists :camp="camp" />
  </SideBar>
</template>

<script>
import SideBar from '@/components/navigation/SideBar.vue'
import MaterialLists from '@/components/material/MaterialLists.vue'
export default {
  name: 'SideBarMaterialLists',
  components: { MaterialLists, SideBar },
  props: {
    camp: { type: Object, required: true },
  },
}
</script>
