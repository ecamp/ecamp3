<template>
  <side-bar>
    <content-card>
      <v-subheader class="text-uppercase subtitle-2">
        Tagesübersicht
      </v-subheader>
      <v-skeleton-loader v-if="eventInstances.loading" class="ma-3"
                         type="list-item@6" />
      <picasso v-else
               :camp="period.camp"
               :event-instances="eventInstances.items"
               :start="startOfDay"
               :interval-height="36"
               :end="endOfDay"
               type="day" />
    </content-card>
  </side-bar>
</template>

<script>
import Picasso from '@/components/camp/Picasso'
import SideBar from '@/components/base/SideBar'
import ContentCard from '@/components/base/ContentCard'

export default {
  name: 'SideBarOverview',
  components: { ContentCard, SideBar, Picasso },
  props: {
    day: { type: Function, required: true }
  },
  computed: {
    period () {
      return this.day().period()
    },
    eventInstances () {
      // TODO add filtering for the current day when backend supports it
      return this.period.event_instances()
    },
    periodStartDate () {
      return new Date(Date.parse(this.period.start))
    },
    startOfDay () {
      return this.addDays(this.periodStartDate, this.day().day_offset)
    },
    endOfDay () {
      return this.addDays(this.startOfDay, 1)
    }
  },
  methods: {
    addDays (date, days) {
      return new Date(date.getTime() + days * 24 * 60 * 60 * 1000)
    }
  }
}
</script>
