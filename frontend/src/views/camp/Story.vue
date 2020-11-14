<!--
Admin screen of a camp: Displays details & periods of a single camp and allows to edit them.
-->

<template>
  <content-card :title="$tc('views.camp.story.title')">
    <v-card-text>
      <template v-for="period in camp().periods().items">
        <h4 :key="period._meta.self">
          {{ period.description }}
        </h4>
        <template v-for="(scheduleEntries, uri) in scheduleEntriesGroupedByDay(period)">
          <h5 :key="uri">
            {{ uri }}
          </h5>
          <template v-for="scheduleEntry in scheduleEntries">
            <div :key="scheduleEntry._meta.self">
              <h6>{{ scheduleEntry.activity().title }}</h6>
              <p v-for="chapter in story(scheduleEntry)" :key="chapter" v-html="chapter" />
            </div>
          </template>
        </template>
      </template>
    </v-card-text>
  </content-card>
</template>

<script>
import ContentCard from '@/components/layout/ContentCard'
import { groupBy, sortBy } from 'lodash'

export default {
  name: 'Story',
  components: {
    ContentCard
  },
  props: {
    camp: { type: Function, required: true }
  },
  data () {
    return {}
  },
  methods: {
    scheduleEntriesGroupedByDay (period) {
      return groupBy(
        sortBy(
          period.scheduleEntries().items,
          scheduleEntry => scheduleEntry.periodOffset
        ),
        scheduleEntry => scheduleEntry.day()._meta.self
      )
    },
    story (scheduleEntry) {
      if (!scheduleEntry.activity().activityContents) return ''
      return scheduleEntry.activity().activityContents().items
        .filter(activityContent => activityContent.contentTypeName === 'Storycontext')
        .map(storyContent => storyContent.singleText().text)
    }
  }
}
</script>

<style scoped>
</style>
