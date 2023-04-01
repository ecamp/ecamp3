<template>
  <div>
    <v-expansion-panels ref="dayPanels" v-model="expandedDays" accordion flat multiple>
      <story-day
        v-for="day in sortedDays"
        :key="day._meta.self"
        :day="day"
        :editing="editing"
      />
    </v-expansion-panels>
  </div>
</template>
<script>
import { sortBy } from 'lodash'
import { nextTick } from 'vue'
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
      this.sortedDays = sortBy(period.days().items, (day) => day.dayOffset)

      nextTick(() => {
        const periodEndInLocalTimezone = this.$date(period.end).add(1, 'days')
        if (periodEndInLocalTimezone.isBefore(this.$date())) {
          this.expandedDays = this.$refs.dayPanels.items.keys()
          return
        }
        this.expandedDays = this.$refs.dayPanels.items
          .map((dayPanel, idx) => {
            const dayInLocalTimezone = this.$date(dayPanel.$attrs['day-date'])
            const dayEndInLocalTimezone = dayInLocalTimezone.add(1, 'days')
            return dayEndInLocalTimezone.isAfter(this.$date()) ? idx : null
          })
          .filter((idx) => !!idx)
      })
    },
  },
}
</script>
