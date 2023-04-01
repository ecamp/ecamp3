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
      expandedDays: [],
    }
  },
  computed: {
    sortedDays() {
      return sortBy(this.period.days().items, (day) => day.dayOffset)
    },
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
      period.days()._meta.load.then(() => {
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
