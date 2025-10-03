<template>
  <ChecklistOverview :checklist-collection="checklistCollection" max-width="900" />
</template>

<script>
import ChecklistOverview from '@/components/checklist/ChecklistOverview.vue'

export default {
  name: 'AdminChecklists',
  components: {
    ChecklistOverview,
  },
  computed: {
    checklistCollection() {
      return this.api.get().checklists({ isPrototype: true })
    },
  },
}
</script>
