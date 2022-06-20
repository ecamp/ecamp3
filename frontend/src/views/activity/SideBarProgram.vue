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
            :start="startOfDay"
            :interval-height="36"
            :end="endOfDay"
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
    startOfDay() {
      return this.addDays(this.period().start, this.day().dayOffset)
    },
    endOfDay() {
      return this.addDays(this.startOfDay, 1)
    },
  },
  methods: {
    addDays(date, days) {
      return Date.parse(date) + days * 24 * 60 * 60 * 1000
    },
  },
}
</script>
