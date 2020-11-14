<template>
  <div>
    <h2>{{ period.description }}</h2>
    <div v-for="(entries, dayUri) in storyContentsGroupedByDay" :key="dayUri">
      <h3>{{ dayName(entries[0].scheduleEntry.day()) }}</h3>
      <div v-for="{ scheduleEntry, storyChapters } in entries" :key="scheduleEntry._meta.uri">
        <h4>{{ scheduleEntry.activity().title }}</h4>
        <p v-for="chapter in storyChapters" :key="chapter" v-html="chapter"></p>
      </div>
    </div>
  </div>
</template>
<script>
import { groupBy, sortBy } from 'lodash'

export default {
  name: 'StoryPeriod',
  props: {
    period: { type: Object, required: true }
  },
  computed: {
    sortedScheduleEntries () {
      return sortBy(this.period.scheduleEntries().items, scheduleEntry => scheduleEntry.periodOffset)
    },
    scheduleEntriesWithStory () {
      return this.sortedScheduleEntries.map(scheduleEntry => {
        return {
          scheduleEntry: scheduleEntry,
          storyChapters: (scheduleEntry.activity().activityContents() || { items: [] })
            .items
            .filter(activityContent => activityContent.contentTypeName === 'Storycontext')
            .map(activityContent => activityContent.singleText().text)
            .filter(text => text)
        }
      }).filter(({ storyChapters }) => storyChapters.length)
    },
    storyContentsGroupedByDay () {
      return groupBy(this.scheduleEntriesWithStory, ({ scheduleEntry }) => scheduleEntry.day()._meta.self)
    }
  },
  methods: {
    dayName (day) {
      const date = this.addDays(this.period.start, day.dayOffset)
      return this.$moment.utc(date).format(this.$tc('global.moment.dateLong'))
    },
    addDays (date, days) {
      return Date.parse(date) + days * 24 * 60 * 60 * 1000
    }
  }
}
</script>
