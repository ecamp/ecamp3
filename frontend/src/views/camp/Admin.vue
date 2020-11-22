<!--
Admin screen of a camp: Displays details & periods of a single camp and allows to edit them.
-->

<template>
  <content-card :title="$tc('views.camp.admin.title')">
    <v-card-text>
      <v-row>
        <v-col cols="12" lg="6">
          <camp-settings :camp="camp" />
        </v-col>
        <v-col cols="12" lg="6">
          <camp-periods :camp="camp" />

          <camp-activity-categories :camp="camp" />
        </v-col>
      </v-row>
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
