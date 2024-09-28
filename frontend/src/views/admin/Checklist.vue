<template>
  <v-container fluid>
    <ChecklistSingle :checklist="checklist" />
  </v-container>
</template>

<script>
import ChecklistSingle from '@/components/checklist/ChecklistSingle.vue'

export default {
  name: 'AdminChecklist',
  components: {
    ChecklistSingle,
  },
  props: {
    checklist: {
      type: Object,
      default: null,
      required: false,
    },
  },
}
</script>
