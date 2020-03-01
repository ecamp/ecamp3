<!--
Displays all periods of a single camp and allows to edit them & create new ones
-->

<template>
  <content-card icon="mdi-calendar-plus" title="Periods">
    <v-skeleton-loader v-if="camp()._meta.loading" type="article" />
    <v-list>
      <period-item
        v-for="period in periods.items"
        :key="period.id"
        :period="period" />

      <v-list-item>
        <v-list-item-content />
        <v-list-item-action>
          <create-period-dialog :camp="camp()">
            <template v-slot:activator="{ on }">
              <button-add v-on="on">
                Create Period
              </button-add>
            </template>
          </create-period-dialog>
        </v-list-item-action>
      </v-list-item>
    </v-list>
  </content-card>
</template>

<script>
import ContentCard from '@/components/base/ContentCard'
import ButtonAdd from '@/components/base/ButtonAdd'
import PeriodItem from '@/components/camp/CampPeriodsListItem'
import CreatePeriodDialog from '@/components/dialog/CreatePeriodDialog'

export default {
  name: 'CampPeriods',
  components: { ContentCard, ButtonAdd, PeriodItem, CreatePeriodDialog },
  props: {
    camp: { type: Function, required: true }
  },
  data () {
    return {
    }
  },
  computed: {
    periods () {
      return this.camp().periods()
    }
  }
}
</script>

<style scoped>
  .v-list-item {
    padding-left: 0;
  }
</style>
