<template>
  <View :id="`${id}-${period.id}-${day.id}`" class="story-day-title-container">
    <Text class="story-day-title">{{ $tc('entity.day.name') }} {{ day.number }}</Text>
    <Text class="story-day-date">{{ date }}</Text>
  </View>
  <template v-for="{ scheduleEntry, storyChapters } in entriesWithStory">
    <template v-for="chapter in storyChapters">
      <View class="story-chapter-title" :min-presence-ahead="30">
        <CategoryLabel
          :category="scheduleEntry.activity().category()"
          style="font-size: 10pt"
        />
        <Text :id="`${id}-${period.id}-${scheduleEntry.id}`" style="margin: 0 3pt">
          {{ scheduleEntry.number }} {{ chapter.title }}
        </Text>
      </View>
      <View style="line-height: 1.6">
        <RichText :rich-text="chapter.data.html" />
      </View>
    </template>
  </template>
</template>
<script>
import PdfComponent from '@/PdfComponent.js'
import { dateLong } from '../../../common/helpers/dateHelperUTCFormatted.js'
import CategoryLabel from '../CategoryLabel.vue'
import RichText from '../RichText.vue'
import sortBy from 'lodash/sortBy.js'
import { isEmptyHtml } from '../helpers.js'

export default {
  name: 'StoryDay',
  components: { RichText, CategoryLabel },
  extends: PdfComponent,
  props: {
    period: { type: Object, required: true },
    day: { type: Object, required: true },
  },
  computed: {
    date() {
      return dateLong(this.day.start, this.$tc)
    },
    sortedScheduleEntries() {
      return sortBy(
        this.period.scheduleEntries().items.filter((scheduleEntry) => {
          return scheduleEntry.day()._meta.self === this.day._meta.self
        }),
        (scheduleEntry) => scheduleEntry.start
      )
    },
    entries() {
      return this.sortedScheduleEntries.map((scheduleEntry) => ({
        scheduleEntry,
        storyChapters: this.period
          .contentNodes()
          .items.filter(
            (contentNode) =>
              contentNode.contentTypeName === 'Storycontext' &&
              contentNode.root()._meta.self ===
                scheduleEntry.activity().rootContentNode()._meta.self &&
              !isEmptyHtml(contentNode.data.html)
          )
          .map((chapter) => ({
            ...chapter,
            title: this.chapterTitle(chapter, scheduleEntry),
          })),
      }))
    },
    entriesWithStory() {
      return this.entries.filter(({ storyChapters }) => storyChapters.length)
    },
  },
  methods: {
    chapterTitle(chapter, scheduleEntry) {
      return (
        scheduleEntry.activity().title +
        (chapter.instanceName ? ' - ' + chapter.instanceName : '')
      )
    },
  },
}
</script>
<pdf-style>
.story-day-title-container {
  display: flex;
  flex-direction: row;
  justify-content: space-between;
  align-items: baseline;
  border-bottom: 2pt solid #aaaaaa;
  padding-bottom: 2pt;
  margin-bottom: 1pt;
}
.story-day-title {
  font-size: 14;
  font-weight: semibold;
  margin: 10pt 0 3pt;
}
.story-day-date {
  font-size: 11pt;
}
.story-chapter-title {
  display: flex;
  flex-direction: row;
  align-items: center;
  font-weight: bold;
  margin: 10pt 0 4.5pt;
}
</pdf-style>
