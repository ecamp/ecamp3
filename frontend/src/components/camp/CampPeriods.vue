<!--
Displays details on a single camp and allows to edit them.
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
          <v-btn
            small
            color="success"
            class="mb-1"
            @click.stop="showCreateDialog = true">
            <i class="v-icon v-icon--left mdi mdi-plus" />
            Create Period
          </v-btn>
        </v-list-item-action>
      </v-list-item>
    </v-list>

    <create-period-dialog v-model="showCreateDialog" :camp="camp()" />
  </content-card>
</template>

<script>
import ContentCard from '@/components/base/ContentCard'
import PeriodItem from '@/components/camp/CampPeriodsListItem'
import CreatePeriodDialog from '../dialog/CreatePeriodDialog'

export default {
  name: 'CampPeriods',
  components: { ContentCard, PeriodItem, CreatePeriodDialog },
  props: {
    camp: { type: Function, required: true }
  },
  data () {
    return {
      showCreateDialog: false
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
