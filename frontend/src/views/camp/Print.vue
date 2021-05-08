<!--
Basic layout for print preview
-->

<template>
  <content-card :title="$tc('views.camp.print.title')" toolbar>
    <v-card-text>
      <router-view />
    </v-card-text>
  </content-card>
</template>

<script>
import ContentCard from '@/components/layout/ContentCard.vue'

export default {
  name: 'Print',
  components: {
    ContentCard
  },
  data () {
    return {}
  }
}
</script>

<style scoped>
</style>
