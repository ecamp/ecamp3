<template>
  <v-container fluid>
    <content-card
      v-if="checklist"
      class="ec-checklist"
      toolbar
      back
      :title="checklist.name"
    >
      <template #title-actions>
        <ChecklistItemCreate :checklist="checklist" />
      </template>
      <v-list>
        <SortableChecklist :parent="null" :checklist="checklist" />
      </v-list>
    </content-card>
  </v-container>
</template>

<script>
import ContentCard from '@/components/layout/ContentCard.vue'
import { campRoleMixin } from '@/mixins/campRoleMixin.js'
import ChecklistItemCreate from '@/components/checklist/ChecklistItemCreate.vue'
import SortableChecklist from '@/components/checklist/SortableChecklist.vue'

export default {
  name: 'Category',
  components: {
    SortableChecklist,
    ChecklistItemCreate,
    ContentCard,
  },
  mixins: [campRoleMixin],
  props: {
    camp: {
      type: Object,
      default: null,
      required: false,
    },
    checklist: {
      type: Object,
      default: null,
      required: false,
    },
  },
  data() {
    return { dragging: false }
  },
  computed: {
    items() {
      return this.checklist.checklistItems().items.filter((item) => !item.parent)
    },
  },
}
</script>
