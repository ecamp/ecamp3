<template>
  <SideBar :title="$tc('entity.checklist.name', 2)" icon="mdi-format-list-numbered">
    <v-list>
      <v-list-item
        v-for="checklist in camp.checklists().items"
        :key="checklist._meta.self"
        :to="checklistRoute(camp, checklist)"
        replace
      >
        <v-list-item-title class="d-flex gap-2 align-baseline">
          <span class="font-weight-medium">{{ checklist.name }}</span>
        </v-list-item-title>
      </v-list-item>
    </v-list>
  </SideBar>
</template>

<script>
import SideBar from '@/components/navigation/SideBar.vue'
import { checklistRoute } from '@/router.js'
export default {
  name: 'SideBarChecklist',
  components: { SideBar },
  props: {
    camp: { type: Object, required: true },
  },
  methods: { checklistRoute },
}
</script>
