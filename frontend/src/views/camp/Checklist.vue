<template>
  <content-card
    :title="$tc('views.camp.checklist.title')"
    toolbar
    :no-border="$vuetify.breakpoint.mdAndUp"
  >
    <v-card-text>
        <v-list class="mx-n2 py-0">
            <v-list-item
                v-for="checklist in checklists"
                :key="checklist._meta.self"
                class="px-2 rounded"
            >
                <v-list-item-content>
                    <v-list-item-title>
                        <h3>{{ checklist.name }}</h3>
                        <ChecklistItemTree
                            v-for="rootChecklistItem in getRootChecklistItems(checklist)"
                            :key="rootChecklistItem._meta.self"
                            :checklistItem="rootChecklistItem"
                        />
                    </v-list-item-title>
                </v-list-item-content>
            </v-list-item>
        </v-list>
    </v-card-text>
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
  computed: {
    checklists() {
      return this.camp.checklists().items
    }
  },
  methods: {
    getRootChecklistItems(checklist) {
      return sortBy(
        checklist.checklistItems().items.filter((c) => c.parent == null),
        (c) => c.position
      )
    }
  }
}
</script>
