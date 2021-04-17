<!--
Show all activity schedule entries of a single period.
-->

<template>
  <content-card>
    <template #title>
      <period-switcher v-if="$vuetify.breakpoint.xsOnly" :period="period" />
    </template>
    <camp-program-view-mode-switcher />
    <schedule-entries :period="period" :show-button="true">
      <template #default="slotProps">
        <template v-if="slotProps.loading">
          <v-skeleton-loader v-if="listFormat" type="list-item-avatar-two-line@2" class="py-2" />
          <v-skeleton-loader v-else type="table" />
        </template>
        <template v-else>
          <picasso
            v-if="!listFormat"
            class="mx-2 ma-sm-0 pa-sm-2"
            :schedule-entries="slotProps.scheduleEntries"
            :period="period"
            :start="Date.parse(period().start)"
            :end="Date.parse(period().end)"
            :dialog-activity-create="slotProps.showActivityCreateDialog"
            :dialog-activity-edit="slotProps.showActivityEditDialog" />
          <activity-list
            v-else
            :schedule-entries="slotProps.scheduleEntries"
            :period="period" />
          <v-alert v-if="listFormat && slotProps.scheduleEntries.length === 0">
            {{ $tc('global.empty.list', 10, { entities: $tc('entity.activity.name', 1) }) }}
          </v-alert>
        </template>
      </template>
    </schedule-entries>
  </content-card>
</template>
<script>
import ContentCard from '@/components/layout/ContentCard.vue'
import Picasso from '@/components/camp/Picasso.vue'
import ActivityList from '@/components/camp/ActivityList.vue'
import ScheduleEntries from '@/components/scheduleEntry/ScheduleEntries.vue'
import PeriodSwitcher from '@/components/camp/PeriodSwitcher.vue'
import CampProgramViewModeSwitcher from '@/components/camp/CampProgramViewModeSwitcher.vue'

export default {
  name: 'CampProgram',
  components: {
    CampProgramViewModeSwitcher,
    PeriodSwitcher,
    ContentCard,
    Picasso,
    ActivityList,
    ScheduleEntries
  },
  props: {
    period: { type: Function, required: true }
  },
  computed: {
    listFormat () {
      return this.$route.query.list
    }
  }
}
</script>

<style lang="scss" scoped>
::v-deep .v-skeleton-loader__list-item-avatar-two-line {
  height: 60px;
}
</style>
