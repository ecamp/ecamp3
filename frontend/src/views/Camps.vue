<template>
  <v-container fluid>
    <content-card :title="$tc('views.camps.title', camps.items.length)" max-width="800" toolbar>
      <MyCamps />
    </content-card>
  </v-container>
</template>

<script>
import ContentCard from '@/components/layout/ContentCard.vue'
import MyCamps from '@/components/MyCamps.vue'

export default {
  name: 'Camps',
  components: {
    MyCamps,
    ContentCard
  },
  computed: {
    camps () {
      return this.api.get().camps()
    }
  },
  mounted () {
    this.api.reload(this.camps)
  }
}
</script>

<style scoped>
</style>
