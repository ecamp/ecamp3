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
        :label="$vuetify.breakpoint.smAndUp ?
          $tc('views.camp.material.showActivityMaterial') :
          $tc('views.camp.material.showActivityMaterialShort')" />
    </v-toolbar>
    <v-card-text>
      <v-expansion-panels v-model="openPeriods" multiple>
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
      openPeriods: [],
      showActivityMaterial: false
    }
  },
  watch: {
    showActivityMaterial (val) {
      localStorage.viewCampMaterialShowActivityMaterial = (val ? 'true' : 'false')
    }
  },
  mounted () {
    if (localStorage.viewCampMaterialShowActivityMaterial === undefined) {
      localStorage.viewCampMaterialShowActivityMaterial = 'false'
    }
    this.showActivityMaterial = (localStorage.viewCampMaterialShowActivityMaterial === 'true')

    this.camp().periods()._meta.load.then(periods => {
      this.openPeriods = periods.items
        .map((period, idx) => Date.parse(period.end) >= new Date() ? idx : null)
        .filter(idx => idx !== null)
    })
  }
}
</script>

<style scoped>
</style>
