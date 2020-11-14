<!--
Admin screen of a camp: Displays details & periods of a single camp and allows to edit them.
-->

<template>
  <content-card :title="$tc('navigation.titles.print')">
    <v-card-text>
      <camp-print :camp="camp" />
    </v-card-text>
  </content-card>
</template>

<script>
import CampPrint from '@/components/camp/CampPrint'
import ContentCard from '@/components/layout/ContentCard'

export default {
  name: 'Admin',
  components: {
    ContentCard,
    CampPrint
  },
  props: {
    camp: { type: Function, required: true }
  },
  data () {
    return {}
  }
}
</script>

<style scoped>
</style>
