<!--
Displays all periods of a single camp and allows to edit them & create new ones
-->

<template>
  <content-group title="Periods">
    <v-skeleton-loader v-if="camp()._meta.loading" type="article" />
    <v-list class="py-0">
      <period-item
        v-for="period in periods.items"
        :key="period.id"
        :period="period" />

      <v-list-item>
        <v-list-item-content />
        <v-list-item-action>
          <dialog-period-create :camp="camp()">
            <template v-slot:activator="{ on }">
              <button-add v-on="on">
                Create Period
              </button-add>
            </template>
          </dialog-period-create>
        </v-list-item-action>
      </v-list-item>
    </v-list>
  </content-group>
</template>

<script>
import ButtonAdd from '@/components/base/ButtonAdd'
import PeriodItem from '@/components/camp/CampPeriodsListItem'
import DialogPeriodCreate from '@/components/dialog/DialogPeriodCreate'
import ContentGroup from '@/components/base/ContentGroup'

export default {
  name: 'CampPeriods',
  components: { ContentGroup, ButtonAdd, PeriodItem, DialogPeriodCreate },
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
