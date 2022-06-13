<!--
Admin screen of a camp: Displays MaterialLists and MaterialItems
-->

<template>
  <content-card :title="$tc('views.camp.material.title')" toolbar>
    <template #title-actions>
      <e-switch
        v-model="showActivityMaterial"
        class="ml-15"
        :label="
          $vuetify.breakpoint.smAndUp
            ? $tc('views.camp.material.showActivityMaterial')
            : $tc('views.camp.material.showActivityMaterialShort')
        " />

      <e-switch
        v-if="$vuetify.breakpoint.smAndUp"
        v-model="groupByList"
        class="ml-15"
        :label="$tc('views.camp.material.groupByList')" />
    </template>
    <v-expansion-panels v-model="openPeriods" multiple
                        flat
                        accordion>
      <period-material-lists
        v-for="period in camp().periods().items"
        :key="period._meta.self"
        :period="period"
        :show-activity-material="showActivityMaterial"
        :group-by-list="groupByList || $vuetify.breakpoint.xs"
        :disabled="!isContributor" />
    </v-expansion-panels>
  </content-card>
</template>

<script>
import ContentCard from '@/components/layout/ContentCard.vue'
import PeriodMaterialLists from '@/components/material/PeriodMaterialLists.vue'
import { campRoleMixin } from '@/mixins/campRoleMixin'

export default {
  name: 'Material',
  components: {
    ContentCard,
    PeriodMaterialLists
  },
  mixins: [campRoleMixin],
  props: {
    camp: { type: Function, required: true }
  },
  data () {
    return {
      openPeriods: [],
      showActivityMaterial: false,
      groupByList: false
    }
  },
  watch: {
    showActivityMaterial (val) {
      localStorage.viewCampMaterialShowActivityMaterial = val ? 'true' : 'false'
    }
  },
  mounted () {
    if (localStorage.viewCampMaterialShowActivityMaterial === undefined) {
      localStorage.viewCampMaterialShowActivityMaterial = 'false'
    }
    this.showActivityMaterial =
      localStorage.viewCampMaterialShowActivityMaterial === 'true'

    this.camp().periods()._meta.load.then(periods => {
      this.openPeriods = periods.items
        .map((period, idx) => Date.parse(period.end) >= new Date() ? idx : null)
        .filter(idx => idx !== null)
    })

    this.camp().activities().$reload()
  }
}
</script>

<style scoped></style>
