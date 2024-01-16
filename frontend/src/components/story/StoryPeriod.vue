<template>
  <v-skeleton-loader v-if="loading" class="mt-2 mt-sm-3" type="list-item-three-line" />
  <v-expansion-panels v-else v-model="expandedDays" variant="accordion" flat multiple>
    <story-day
      v-for="day in sortedDays"
      :key="day._meta.self"
      :day="day"
      :editing="editing"
      :period-story-chapters="periodStoryChapters.items"
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
      contentTypeStorycontext: null,
      loading: true,
    }
  },
  computed: {
    sortedDays() {
      if (!this.period.days()._meta.loading) {
        return sortBy(this.period.days().items, (day) => day.dayOffset)
      }
      return []
    },
    periodStoryChapters() {
      return this.api.get().contentNodes({
        period: this.period._meta.self,
        contentType: this.contentTypeStorycontext._meta.self,
      })
    },
  },
  watch: {
    period: {
      immediate: true,
      async handler(newPeriod, oldPeriod) {
        if (newPeriod?._meta.self !== oldPeriod?._meta.self) {
          await this.updateComponentData(newPeriod)
        }
      },
    },
  },
  methods: {
    async updateComponentData(period) {
      this.loading = true

      if (!this.contentTypeStorycontext) {
        this.contentTypeStorycontext = (
          await this.api.get().contentTypes().$loadItems()
        ).items.find((contentType) => contentType.name === 'Storycontext')
      }

      // Reload data after changing period
      this.periodStoryChapters.$reload()
      this.period.scheduleEntries().$reload()
      this.period
        .days()
        .$reload()
        .then(() => this.computeExpandedDays(period))

      // ensures we have loaded all relevant data, but this will not wait for the $reloads above
      await Promise.all([
        this.periodStoryChapters.$loadItems(),
        this.period.scheduleEntries().$loadItems(),
        this.period.days().$loadItems(),
        this.period.camp().activities().$loadItems(),
        this.period.camp().categories().$loadItems(),
      ])

      this.loading = false

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
        .filter((dayKey) => !!dayKey)
    },
  },
}
</script>
