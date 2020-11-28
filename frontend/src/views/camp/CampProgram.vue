<!--
Show all activity schedule entries of a single period.
-->

<template>
  <content-card>
    <v-sheet>
      <period-switcher v-if="$vuetify.breakpoint.xsOnly" :period="period" />
      <v-btn-toggle class="view_mode--switcher ma-3" dense rounded>
        <v-btn :to="{ query: { ...$route.query, list: true } }" exact>
          <v-icon>mdi-format-list-numbered</v-icon>
        </v-btn>
        <v-btn :to="{ query: { ...Array.from($route.query).map(({list, ...rest }) => rest ) } }" exact>
          <v-icon>mdi-calendar-month</v-icon>
        </v-btn>
      </v-btn-toggle>
      <schedule-entries :period="period" :show-button="true">
        <template v-slot:default="slotProps">
          <template v-if="slotProps.loading">
            <v-skeleton-loader v-if="listFormat" type="list-item-avatar-two-line@2" class="py-2" />
            <v-skeleton-loader v-else type="table" />
          </template>
          <template v-else>
            <picasso
              v-show="!listFormat"
              class="mx-2 ma-sm-0 pa-sm-2"
              :schedule-entries="slotProps.scheduleEntries"
              :period="period"
              :start="Date.parse(period().start)"
              :end="Date.parse(period().end)"
              :dialog-activity-create="slotProps.showActivityCreateDialog"
              :dialog-activity-edit="slotProps.showActivityEditDialog" />
            <activity-list
              v-show="listFormat"
              :schedule-entries="slotProps.scheduleEntries"
              :period="period" />
          </template>
        </template>
      </schedule-entries>
    </v-sheet>
  </content-card>
</template>
<script>
import ContentCard from '@/components/layout/ContentCard'
import Picasso from '@/components/camp/Picasso'
import ActivityList from '@/components/camp/ActivityList'
import ScheduleEntries from '@/components/scheduleEntry/ScheduleEntries'
import PeriodSwitcher from '@/components/camp/PeriodSwitcher'

export default {
  name: 'CampProgram',
  components: {
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
      return !!this.$route.query.list
    }
  }
}
</script>

<style lang="scss" scoped>
::v-deep .v-skeleton-loader__list-item-avatar-two-line {
  height: 60px;
}

.view_mode--switcher {
  opacity: .6;

  ::v-deep .v-btn {
    min-width: initial;
  }
}

.view_mode--switcher:hover {
  opacity: 1;
}

@media #{map-get($display-breakpoints, 'xs-only')}{
  .view_mode--switcher {
    position: fixed;
    z-index: 10;
    right: 0;
    top: 0;
  }
}

@media #{map-get($display-breakpoints, 'sm-and-up')}{
  .view_mode--switcher {
    position: fixed;
    z-index: 10;
    right: 8px;
    top: 73px !important;
  }
}
</style>
