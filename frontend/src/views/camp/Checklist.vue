<template>
  <content-card
    :title="$tc('views.camp.checklist.title')"
    toolbar
    :no-border="$vuetify.breakpoint.mdAndUp"
  >
    <v-expansion-panels v-model="expandedChecklists" accordion flat multiple>
      <v-expansion-panel v-for="checklist in checklists" :key="checklist._meta.self">
        <v-expansion-panel-header>
          <h3>{{ checklist.name }}</h3>
        </v-expansion-panel-header>
        <v-expansion-panel-content>
          <ChecklistItemTree
            v-for="rootChecklistItem in getRootChecklistItems(checklist)"
            :key="rootChecklistItem._meta.self"
            :checklist-item="rootChecklistItem"
          />
        </v-expansion-panel-content>
      </v-expansion-panel>
    </v-expansion-panels>
  </content-card>
</template>

<script>
import { sortBy } from 'lodash'
import ContentCard from '@/components/layout/ContentCard.vue'
import ChecklistItemTree from '@/components/checklist/ChecklistItemTree.vue'

export default {
  name: 'Checklist',
  components: {
    ContentCard,
    ChecklistItemTree,
  },
  props: {
    camp: { type: Object, required: true },
  },
  data() {
    return {
      expandedChecklists: [0],
    }
  },
  computed: {
    checklists() {
      return this.camp.checklists().items
    },
  },
  async mounted() {
    this.checklists.map((cl) => {
      cl.checklistItems().$reload()
      cl.$reload()
    })
    await this.camp.checklists().$reload()
  },
  methods: {
    getRootChecklistItems(checklist) {
      return sortBy(
        checklist.checklistItems().items.filter((c) => c.parent == null),
        (c) => c.position
      )
    },
  },
}
</script>
