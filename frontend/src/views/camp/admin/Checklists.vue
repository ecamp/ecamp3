<template>
  <ChecklistOverview :camp="camp" :checklist-collection="checklistCollection" />
</template>

<script>
import ChecklistOverview from '@/components/checklist/ChecklistOverview.vue'

export default {
  name: 'CampAdminChecklists',
  components: {
    ChecklistOverview,
  },
  props: {
    camp: { type: Object, required: true },
  },
  computed: {
    checklistCollection() {
      return this.camp.checklists()
    },
  },
}
</script>
