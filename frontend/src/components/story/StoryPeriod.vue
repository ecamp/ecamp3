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
      if (!this.period.days()._meta.loading) {
        return sortBy(this.period.days().items, (day) => day.dayOffset)
      }
      return []
    },
  },
  watch: {
    period: {
      immediate: true,
      async handler(newPeriod, oldPeriod) {
        const newPeriodId = newPeriod == undefined ? null : newPeriod.id
        const oldPeriodId = oldPeriod == undefined ? null : oldPeriod.id
        if (newPeriodId != oldPeriodId) {
          await this.updateComponentData(newPeriod)
        }
      },
    },
  },
  methods: {
    async updateComponentData(period) {
      // show days in store immediately
      this.computeExpandedDays(period)

      // reload days of period to ensure all days are loaded
      await period.days().$reload()

      // show reloaded days
      this.computeExpandedDays(period)
    },
    computeExpandedDays(period) {
      const periodEndInLocalTimezone = this.$date(period.end).add(1, 'days')
      const periodHasPassed = periodEndInLocalTimezone.isBefore(this.$date())

      this.expandedDays = this.sortedDays
        .map((day) => {
          const dayKey = day.start.substr(0, 10)
          const dayEndInLocalTimezone = this.$date(day.end.substr(0, 10))
          const dayEndIsInTheFuture = dayEndInLocalTimezone.isAfter(this.$date())

          return periodHasPassed || dayEndIsInTheFuture ? dayKey : null
        })
        .filter((idx) => !!idx)
    },
  },
}
</script>
