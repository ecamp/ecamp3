<template>
  <v-container fluid>
    <ChecklistDetail :camp="camp" :checklist="checklist" />
  </v-container>
</template>

<script>
import ChecklistDetail from '@/components/checklist/ChecklistDetail.vue'

export default {
  name: 'Checklist',
  components: {
    ChecklistDetail,
  },
  props: {
    camp: { type: Object, required: true },
    checklist: {
      type: Object,
      default: null,
      required: false,
    },
  },
}
</script>
