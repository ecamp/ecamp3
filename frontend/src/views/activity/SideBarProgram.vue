<template>
  <SideBar
    :title="$tc('views.activity.sideBarProgram.title')"
    icon="mdi-format-list-numbered"
  >
    <ScheduleEntries :period="period" :show-button="false">
      <template #default="slotProps">
        <v-skeleton-loader v-if="slotProps.loading" class="ma-3" type="list-item@6" />
        <Picasso
          v-else
          :schedule-entries="slotProps.scheduleEntries"
          :period="period()"
          :start="currentDayAsString"
          :interval-height="36"
          :end="currentDayAsString"
          type="day"
        />
      </template>
    </ScheduleEntries>
  </SideBar>
</template>

<script>
import Picasso from '@/components/program/picasso/Picasso.vue'
import SideBar from '@/components/navigation/SideBar.vue'
import ScheduleEntries from '@/components/program/ScheduleEntries.vue'

import { HTML5_FMT } from '@/common/helpers/dateFormat.js'

export default {
  name: 'SideBarProgram',
  components: { SideBar, Picasso, ScheduleEntries },
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
