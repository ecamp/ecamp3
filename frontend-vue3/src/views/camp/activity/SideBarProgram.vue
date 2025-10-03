<template>
  <SideBar
    :title="$t('views.camp.activity.sideBarProgram.title')"
    icon="mdi-format-list-numbered"
  >
    <ScheduleEntries v-if="period" :period="period" :show-button="false">
      <template #default="slotProps">
        <DaySwitcher
          :camp="camp"
          :day-selection="daySelection"
          :loading="slotProps.loading"
          @change-day="selectedDay = $event"
        />
        <v-divider />
        <v-skeleton-loader v-if="slotProps.loading" class="mx-1" type="list-item@6" />
        <Picasso
          v-else
          class="ec-sidebar-program__picasso"
          :schedule-entries="slotProps.scheduleEntries"
          :period="period"
          :start="currentDayAsString"
          :interval-height="36"
          :end="currentDayAsString"
          type="day"
        >
          <template #day-label-header><span hidden></span></template>
        </Picasso>
      </template>
    </ScheduleEntries>
  </SideBar>
</template>

<script>
import Picasso from '@/components/program/picasso/Picasso.vue'
import SideBar from '@/components/navigation/SideBar.vue'
import ScheduleEntries from '@/components/program/ScheduleEntries.vue'

import { HTML5_FMT } from '@/common/helpers/dateFormat.js'
import DaySwitcher from '@/components/activity/DaySwitcher.vue'
import { firstActivityScheduleEntry } from '@/router.js'
import scheduleEntryRouteChange from '@/helpers/scheduleEntryRouteChange.js'

export default {
  name: 'SideBarProgram',
  components: { DaySwitcher, SideBar, Picasso, ScheduleEntries },
  async beforeRouteUpdate(to, from, next) {
    return scheduleEntryRouteChange(this.activityId, to, from, next)
  },
  props: {
    camp: { type: Object, required: true },
    activityId: { type: String, required: true },
    scheduleEntryId: { type: String, default: null },
  },
  data() {
    return {
      selectedDay: null,
      scheduleEntry: null,
    }
  },
  computed: {
    day() {
      return this.scheduleEntry.day()
    },
    period() {
      return this.daySelection.period()
    },
    daySelection() {
      return this.selectedDay ?? this.day
    },
    currentDayAsString() {
      return this.$date.utc(this.daySelection.start).format(HTML5_FMT.DATE)
    },
  },
  watch: {
    scheduleEntryId: {
      async handler(id) {
        try {
          this.scheduleEntry = this.api.get().scheduleEntries({ id })
        } catch {
          this.scheduleEntry = await firstActivityScheduleEntry(this.activityId)
        }
      },
      immediate: true,
    },
  },
}
</script>

<style scoped lang="scss">
@use '/src/scss/variables';

.ec-sidebar-program__picasso :deep(.e-picasso) {
  @media #{map-get(variables.$display-breakpoints, 'md-and-up')} {
    height: calc(100vh - 202px);
  }
}

.ec-sidebar-program__picasso :deep(.v-calendar-daily__head) {
  display: none;
}
</style>
