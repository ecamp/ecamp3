<template>
  <Text :id="`${id}-${period.id}-${day.id}`" class="h2"
    >{{ $tc('entity.day.name') }} {{ day.number }} ({{ date }})</Text
  >
  <template
    v-for="{ scheduleEntry, storyChapters } in entriesWithStory"
    :key="scheduleEntry.id"
  >
    <template v-for="(chapter, idx) in storyChapters" :key="idx">
      <View class="h3 story-chapter-title" :min-presence-ahead="30">
        <Text :id="`${id}-${period.id}-${scheduleEntry.id}`">
          {{ scheduleEntry.number }}
        </Text>
        <CategoryLabel
          :category="scheduleEntry.activity().category()"
          style="margin: 0 3pt"
        />
        <Text>{{ chapter.title }}</Text>
      </View>
      <RichText :rich-text="chapter.data.html" />
    </template>
  </template>
</template>
<script>
import PdfComponent from '@/PdfComponent.js'
import { dateLong } from '@/common/helpers/dateHelperUTCFormatted.js'
import CategoryLabel from '../CategoryLabel.vue'
import RichText from '../RichText.vue'
import sortBy from 'lodash/sortBy'

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
              !this.isEmptyHtml(contentNode.data.html)
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
    isEmptyHtml(html) {
      if (html === null) {
        return true
      }

      return html.trim() === '' || html.trim() === '<p></p>'
    },
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
.story-chapter-title {
  display: flex;
  flex-direction: row;
  margin-top: 10pt;
}
</pdf-style>
