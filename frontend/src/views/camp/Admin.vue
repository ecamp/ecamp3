<!--
Admin screen of a camp: Displays details & periods of a single camp and allows to edit them.
-->

<template>
  <content-card title="Admin">
    <v-card-text>
      <camp-settings :camp="camp" />

      <camp-periods :camp="camp" />

      <api-demo :camp="camp" />
    </v-card-text>
  </content-card>
</template>

<script>
import CampSettings from '@/components/camp/CampSettings'
import CampPeriods from '@/components/camp/CampPeriods'
import ApiDemo from '@/components/camp/ApiDemo'
import ContentCard from '@/components/layout/ContentCard'

export default {
  name: 'Admin',
  components: { ContentCard, CampSettings, CampPeriods, ApiDemo },
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
