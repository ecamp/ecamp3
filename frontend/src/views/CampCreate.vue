<template>
  <v-container fluid>
    <content-card max-width="800" :title="$tc('views.campCreate.title')" toolbar>
      <CampCreate />
    </content-card>
  </v-container>
</template>

<script>
import ContentCard from '@/components/layout/ContentCard.vue'
import CampCreate from '@/components/campCreate/CampCreate.vue'

export default {
  name: 'Camps',
  components: {
    CampCreate,
    ContentCard,
  },
}
</script>
