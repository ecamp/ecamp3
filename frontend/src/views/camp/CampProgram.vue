<!--
Show all activity schedule entries of a single period.
-->

<template>
  <content-card>
    <v-sheet>
      <search-mobile v-if="$vuetify.breakpoint.xs" />
      <v-btn
        :fixed="$vuetify.breakpoint.xs"
        :absolute="!$vuetify.breakpoint.xs"
        light class="fab--top_nav"
        fab small
        style="z-index: 3"
        top right
        :class="{'float-right':!$vuetify.breakpoint.xs}"
        color="white"
        :to="{ query: { ...$route.query, list: !listFormat || undefined } }">
        <v-icon v-if="listFormat">mdi-calendar-month</v-icon>
        <v-icon v-else>mdi-menu</v-icon>
      </v-btn>
      <schedule-entries :period="period" :show-button="true">
        <template v-slot:default="slotProps">
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
          </template>
        </template>
      </schedule-entries>
    </v-sheet>
  </content-card>
</template>
<script>
import ContentCard from '@/components/layout/ContentCard'
import SearchMobile from '@/components/navigation/SearchMobile'
import Picasso from '@/components/camp/Picasso'
import ActivityList from '@/components/camp/ActivityList'
import ScheduleEntries from '@/components/scheduleEntry/ScheduleEntries'

export default {
  name: 'CampProgram',
  components: {
    ContentCard,
    SearchMobile,
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
