<template>
  <content-card
    v-if="checklist"
    :key="checklist._meta.self"
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
</template>

<script>
import ContentCard from '@/components/layout/ContentCard.vue'
import ChecklistItemCreate from '@/components/checklist/ChecklistItemCreate.vue'
import SortableChecklist from '@/components/checklist/SortableChecklist.vue'

export default {
  name: 'ChecklistDetail',
  components: {
    SortableChecklist,
    ChecklistItemCreate,
    ContentCard,
  },
  props: {
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
