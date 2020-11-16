<!--
Admin screen of a camp: Displays MaterialLists and MaterialItems
-->

<template>
  <content-card>
    <v-toolbar>
      <v-card-title>{{ $tc('views.camp.material.title') }}</v-card-title>
      <v-spacer />
      <e-switch
        v-model="showActivityMaterial"
        :label="$tc('views.camp.material.showActivityMaterial')" />
    </v-toolbar>
    <v-card-text>
      <v-expansion-panels multiple>
        <period-material-lists v-for="period in camp().periods().items"
                               :key="period._meta.self"
                               :period="period"
                               :show-activity-material="showActivityMaterial" />
      </v-expansion-panels>
    </v-card-text>
  </content-card>
</template>

<script>
import ContentCard from '@/components/layout/ContentCard'
import PeriodMaterialLists from '@/components/camp/PeriodMaterialLists'

export default {
  name: 'Material',
  components: {
    ContentCard,
    PeriodMaterialLists
  },
  props: {
    camp: { type: Function, required: true }
  },
  data () {
    return {
      showActivityMaterial: false
    }
  }
}
</script>

<style scoped>
</style>
