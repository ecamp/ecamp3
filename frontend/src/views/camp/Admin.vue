<!--
Admin screen of a camp: Displays details & periods of a single camp and allows to edit them.
-->

<template>
  <content-card :title="$tc('components.navigation.adminTitle')">
    <v-card-text>
      <camp-settings :camp="camp" />

      <camp-periods :camp="camp" />

      <camp-activity-categories :camp="camp" />
    </v-card-text>
  </content-card>
</template>

<script>
import CampSettings from '@/components/camp/CampSettings'
import CampPeriods from '@/components/camp/CampPeriods'
import CampActivityCategories from '@/components/camp/CampActivityCategories'
import ContentCard from '@/components/layout/ContentCard'

export default {
  name: 'Admin',
  components: {
    ContentCard,
    CampSettings,
    CampPeriods,
    CampActivityCategories
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
