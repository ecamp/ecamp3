<template>
  <content-card :title="$tc('views.camp.checklistOverview.checklistLists.title')" toolbar>
    <v-list>
      <v-skeleton-loader v-if="checklists._meta.loading" type="list-item@3" />
      <v-list-item
        v-for="checklist in checklists.items"
        :key="checklist._meta.self"
        :to="checklistOverviewRoute(camp, checklist, { isDetail: true })"
        exact-path
      >
        <v-list-item-content>
          <v-list-item-title>{{ checklist.name }}</v-list-item-title>
        </v-list-item-content>
        <v-list-item-icon>
          <v-icon color="blue-grey lighten-3">mdi-chevron-right</v-icon>
        </v-list-item-icon>
      </v-list-item>
    </v-list>
  </content-card>
</template>

<script>
import ContentCard from '@/components/layout/ContentCard.vue'
import { checklistOverviewRoute } from '@/router'

export default {
  name: 'ChecklistLists',
  components: {
    ContentCard,
  },
  props: {
    camp: { type: Object, required: true },
  },
  computed: {
    checklists() {
      return this.camp.checklists()
    },
  },
  mounted() {
    this.checklists.$loadItems()
  },
  methods: {
    checklistOverviewRoute,
  },
}
</script>
