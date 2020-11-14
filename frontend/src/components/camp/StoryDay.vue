<template>
  <div>
    <h3>{{ dayName }}</h3>
    <template v-if="entriesWithStory.length">
      <div v-for="{ scheduleEntry, storyChapters } in entriesWithStory" :key="scheduleEntry._meta.uri">
        <h4>{{ scheduleEntry.activity().title }}</h4>
        <template v-if="editing">
          <api-form v-for="chapter in storyChapters" :key="chapter._meta.uri" :entity="chapter">
            <api-textarea
              fieldname="text"
              label=""
              auto-grow
              :outlined="false"
              :solo="false" />
          </api-form>
        </template>
        <template v-else>
          <tiptap-editor v-for="chapter in storyChapters" :key="chapter._meta.uri" :value="chapter.text" />
        </template>
      </div>
    </template>
    <div v-else class="grey--text">
      {{ $tc('components.camp.storyDay.noStory') }}
    </div>
  </div>
</template>
<script>
import { sortBy } from 'lodash'
import ApiForm from '@/components/form/api/ApiForm'
import ApiTextarea from '@/components/form/api/ApiTextarea'
import TiptapEditor from '@/components/form/tiptap/TiptapEditor'

export default {
  name: 'StoryDay',
  components: { TiptapEditor, ApiForm, ApiTextarea },
  props: {
    day: { type: Object, required: true },
    editing: { type: Boolean, default: false }
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
