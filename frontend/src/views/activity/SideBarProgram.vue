<template>
  <side-bar>
    <content-card>
      <v-subheader class="text-uppercase subtitle-2">
        {{ $tc('views.activity.sideBarProgram.title') }}
      </v-subheader>
      <schedule-entries :period="period" :show-button="false">
        <template #default="slotProps">
          <v-skeleton-loader v-if="slotProps.loading" class="ma-3" type="list-item@6" />
          <picasso
            v-else
            :schedule-entries="slotProps.scheduleEntries"
            :period="period()"
            :start="currentDayAsString"
            :interval-height="36"
            :end="currentDayAsString"
            type="day"
          />
        </template>
      </schedule-entries>
    </content-card>
  </side-bar>
</template>

<script>
import Picasso from '@/components/program/picasso/Picasso.vue'
import SideBar from '@/components/navigation/SideBar.vue'
import ContentCard from '@/components/layout/ContentCard.vue'
import ScheduleEntries from '@/components/program/ScheduleEntries.vue'

import { HTML5_FMT } from '@/common/helpers/dateFormat.js'

export default {
  name: 'SideBarProgram',
  components: { ContentCard, SideBar, Picasso, ScheduleEntries },
  props: {
    day: { type: Function, required: true },
  },
  computed: {
    period() {
      return this.day().period
    },
    currentDayAsString() {
      return this.$date.utc(this.day().start).format(HTML5_FMT.DATE)
    },
  },
}
</script>
