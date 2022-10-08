<template>
  <v-expansion-panels v-model="expandedDays" accordion flat multiple>
    <story-day
      v-for="day in period.days().items"
      :key="day._meta.self"
      :day="day"
      :editing="editing"
    />
  </v-expansion-panels>
</template>
<script>
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
      period.days()._meta.load.then((days) => {
        const now = new Date()
        if (Date.parse(period.end) < now) {
          this.expandedDays = [...Array(days.items.length).keys()]
          return
        }
        this.expandedDays = days.items.map((day, idx) =>
          Date.parse(day.end) >= now ? idx : null
        )
      })
    },
  },
}
</script>
