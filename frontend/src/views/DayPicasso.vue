<template>
  <v-skeleton-loader v-if="eventInstances.loading" class="ma-3"
                     type="list-item@6" />
  <picasso v-else
           :camp="period.camp"
           :event-instances="eventInstances.items"
           :start="startOfDay"
           :end="endOfDay"
           type="day" />
</template>

<script>
import Picasso from '../components/Picasso'
export default {
  name: 'DayPicasso',
  components: { Picasso },
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
