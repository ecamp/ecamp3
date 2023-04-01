<template>
  <v-expansion-panels v-model="expandedDays" accordion flat multiple>
    <story-day
      v-for="day in sortedDays"
      :key="day._meta.self"
      :day="day"
      :editing="editing"
    />
  </v-expansion-panels>
</template>
<script>
import { sortBy } from 'lodash'
import StoryDay from './StoryDay.vue'

export default {
  name: 'StoryPeriod',
  components: { StoryDay },
  props: {
    period: { type: Object, required: true },
    editing: { type: Boolean, default: false },
  },
  data() {
    return {
      sortedDays: [],
      expandedDays: [],
    }
  },
  watch: {
    period: {
      immediate: true,
      handler(value) {
        this.computeExpandedDays(value)
      },
    },
  },
  methods: {
    computeExpandedDays(period) {
      let days = period.days()

      // show days in store immediately
      this.sortedDays = sortBy(days.items, (day) => day.dayOffset)
      this.expandedDays = [...Array(this.sortedDays.length).keys()]

      // reload days of period to ensure all days are loaded
      days.$reload().then(() => {
        this.sortedDays = sortBy(this.period.days().items, (day) => day.dayOffset)

        const periodEndInLocalTimezone = this.$date(period.end).add(1, 'days')
        if (periodEndInLocalTimezone.isBefore(this.$date())) {
          this.expandedDays = [...Array(this.sortedDays.length).keys()]
          return
        }
        this.expandedDays = this.sortedDays.map((day, idx) => {
          const dayInLocalTimezone = this.$date(day.end.substr(0, 10))
          return dayInLocalTimezone.isAfter(this.$date()) ? idx : null
        })
      })
    },
  },
}
</script>
