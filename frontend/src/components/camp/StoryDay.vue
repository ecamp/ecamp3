<template>
  <div>
    <h3>{{ dayName }}</h3>
    <template v-if="entriesWithStory.length">
      <div v-for="{ scheduleEntry, storyChapters } in entriesWithStory" :key="scheduleEntry._meta.uri">
        <h4>{{ scheduleEntry.activity().title }}</h4>
        <!-- eslint-disable-next-line vue/no-v-html TODO in the future we should probably use tiptap to render html -->
        <p v-for="chapter in storyChapters" :key="chapter._meta.uri" v-html="chapter.text" />
      </div>
    </template>
    <div v-else class="grey--text">
      {{ $tc('components.camp.storyDay.noStory')}}
    </div>
  </div>
</template>
<script>
import { sortBy } from 'lodash'

export default {
  name: 'StoryDay',
  props: {
    day: { type: Object, required: true }
  },
  computed: {
    dayName () {
      const date = this.addDays(this.day.period().start, this.day.dayOffset)
      return this.$moment.utc(date).format(this.$tc('global.moment.dateLong'))
    },
    sortedScheduleEntries () {
      return sortBy(this.day.scheduleEntries().items, scheduleEntry => scheduleEntry.periodOffset)
    },
    entries () {
      return this.sortedScheduleEntries.map(scheduleEntry => {
        return {
          scheduleEntry: scheduleEntry,
          storyChapters: (scheduleEntry.activity().activityContents() || { items: [] })
            .items
            .filter(activityContent => activityContent.contentTypeName === 'Storycontext')
            .map(activityContent => activityContent.singleText())
            .filter(text => text.text)
        }
      })
    },
    entriesWithStory () {
      return this.entries.filter(({ storyChapters }) => storyChapters.length)
    }
  },
  methods: {
    addDays (date, days) {
      return Date.parse(date) + days * 24 * 60 * 60 * 1000
    }
  }
}
</script>
